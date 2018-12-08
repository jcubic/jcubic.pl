---
layout: post
title:  "System komentarzy HashOver jako alternatywa dla Disqus"
date:   2018-12-08 18:26:02+0100
categories:
tags: php biblioteki
author: jcubic
description: Disqus dodaje masę smieci i reklam, HashOver to biblioteka OSS, która jest alternatywą. Możesz ją dodać do swojego bloga. Wpis zawera także opis jak zaimportować dane z Disqus.
image:
 url: "/img/comments.jpg"
 alt: "Napis 'Comments' po anielsku"
 width: 800
 height: 533
 attribution: "Autor [Tookapi](https://www.pexels.com/@tookapic) źródło [pexels.com](https://www.pexels.com/photo/blue-comments-facebook-pixels-75416/) licencja własna"
---

W zeszły miesiącu usunąłem komentarze Disqus, zastępując je aplikacją
[HashOver](https://github.com/jacobwb/hashover-next) (w wersji next, czyli to co kiedyś będzie
HashOver 2.0), która jest dostępna na licencji
[GNU Affero GPL](https://pl.wikipedia.org/wiki/Affero_General_Public_License) i napisana w PHP, dlatego
można jej użyć, gdy masz swój blog na współdzielonym hostingu tak jak ja. Nawet gdy jest to blog
plików statycznych, tak jak Głównie JavaScript.

Nie musicie się przejmować wersją licencji Affero, która wymusza udostępnienie kodu źródłowego,
nawet jeśli mamy aplikacje gdzieś na serwerze i jej nie kopiujemy, ponieważ zawiera wbudowaną
przeglądarkę kodu PHP.

Zmiana systemu komentarzy była spowodowana tym, że w konsoli dostawałem 404 z jakiejś dziwnej
domeny i były jakieś dziwne ciasteczka. Disqus dodaje też reklamy, na szczęście tylko jeśli ma się
odpowiednio duży ruch na stronie, którego jeszcze nie przekroczyłem. W tym wpisie przedstawie jak
dodać HashOver do strony, który jest dość prosty, ale najważniejszą częścią będzie, jak **zaimportować**
komentarze z **Disqus**.

<!-- more -->

### Instalacja

Instalacja HashOver Next jest dość prosta. Musimy wgrać pliki na serwer i zmodyfikować plik
`hashover/backend/classes/secret.php`. Miejsce pliku może się zmienić, we wcześniejszej wersji był w katalogu `hashover/scripts`.
Plik powinien się raczej znajdować w katalogu `backend` bo nie jest to klasa.

Do pliku musimy wpisać hasło, email i klucz szyfrujący.

Jako klucz można użyć np. hasz MD5 jakiegoś słowa. Po wgraniu na serwer wystarczy na każdej
stronie wpisu swojego bloga dodać:

{% highlight html %}
<script type="text/javascript"
        src="/hashover-next/hashover/comments.php"></script>
<noscript>You must have JavaScript enabled to use the comments.</noscript>
{% endhighlight %}

I to w zasadzie tyle, tylko jeśli wcześniej mieliśmy komentarze Disqus to stracimy stare
komentarze. Wiec trzeba by je jakoś zaimportować.

### Import komentarzy z Disqus

DL;DR cały kod jest na [GitHubie](https://github.com/jcubic/disqus-hashover-import).

Pierwszą rzeczą jaką sprawdziłem był eksport danych z ich panelu, niestety nie ma w nich informacji
i awatarach, więc każdy musiałby mieć jakieś randomowe. Ja chciałem mieć ikonki użytkowników więc
postanowiłem napisać skrypt który zassie ich dane z API.  Skrypt napisałem w PHP ponieważ pomyślałem
że może się przydać do projektu (który jest napisany w PHP).  Mogli by dodać opcję importu do aplikacji.


### Oficjalna biblioteka PHP

Disqus na GitHubie ma swoją oficjalną bibliotekę do PHP. Problem jest taki że nie
obsługuje stronicowania i chyba już nie będzie bo nikt się tą biblioteką nie zajmuje.

Ale dzięki temu, że jest Open Source można kod poprawić, a wystarczy zmienić linijkę:

{% highlight php startinline=true %}
return $data->response;
{% endhighlight %}

na

{% highlight php startinline=true %}
return $data;
{% endhighlight %}

Dzięki temu będziemy mieli dostęp do kursora, po danych. Bazując na dokumentacji API
napisałem taki kod:

{% highlight php startinline=true %}
require('disqus-php/disqusapi/disqusapi.php');
$disqus = new DisqusAPI('<API KEY>');

function fetch($options, $fn, $cursor = NULL) {
    if ($cursor != NULL) {
        $payload = array_merge($options, array('cursor' => $cursor));
    } else {
        $payload = $options;
    }
    $res = $fn($payload);
    $posts = $res->response;
    if ($res->cursor->hasNext) {
        $posts = array_merge($posts, fetch($options, $fn, $res->cursor->next));
    }
    return $posts;
}

$opts = array('forum' => 'gjavascript');
// use limit: 100 max option if you have lots of comments

$posts = fetch($opts, function($payload) use ($disqus) {
    return $disqus->posts->list($payload);
});
$treads = fetch($opts, function($payload) use ($disqus) {
    return $disqus->threads->list($payload);
});
{% endhighlight %}

żeby nie przekroczyć limitu przy pisaniu kodu pobrałem najpierw wszystkie wątki i wpisu
i zapisałem je w pliku JSON.

{% highlight php startinline=true %}
function save($fname, $obj) {
    $f = fopen($fname, 'w');
    fwrite($f, json_encode($obj, JSON_PRETTY_PRINT));
    fclose($f);
}
save('posts.json', $posts);
save('threads.json', $treads);
{% endhighlight %}

Aby ten kod zadziałał będziesz musiał wygenerować klucz API czyli stworzyć aplikacje. Możesz spróbować bez klucza
ponieważ według konsoli do testowania API nie jest wymagany. Ale ja tego nie testowałem.

### Generowanie komentarzy

Komentarze w HashOver znajdują się w katalogu `hashover/comments/threads`. Każdy wątek,
czyli wpis na blogu, ma swój katalog, w którym znajdują się pliki xml z nazwami np.
`1.xml` czy `1-1.xml`, oznaczający komentarz pierwszy i odpowiedź od komentarza pierwszego.

W każdym katalogu znajdował się też katalog z meta danymi tj. plikiem JSON, w którym była tablica z
posortowanymi wpisami, ale HashOver dział bez tego katalogu i pliku więc go zignorowałem.

Kod który napisałem do wygenerowania komentarzy, wygląda tak:

{% highlight php startinline=true %}
function addChild($xml, $node, $name, $value) {
    $element = $xml->createElement($name);
    $element->appendChild($xml->createTextNode($value));
    $node->appendChild($element);
    return $element;
}

foreach ($threads as $thread) {
    $dir_name = getSafeThreadName(preg_replace("%^https?://[^/]+%", "", $thread['link']));
    $dir = 'threads/' . $dir_name;
    // można by zoptymalizować ten kod i sprawdać czy są jakieś komentarze zanim utworzymy katalog
    if (!is_dir($dir)) {
        mkdir($dir);
        chmod($dir, 0775);
    }
    // wszystkie wywołania echo są tylko do debugowania
    echo str_repeat('-', 80) . "\n";
    echo ":: " . $thread['clean_title'] . "\n";
    echo str_repeat('-', 80) . "\n";
    foreach ($posts as $post) {
        $post['children'] = array_filter($posts, function($p) use ($post) {
            return $p['parent'] = $post['id'];
        });
    }
    $root = array_filter($posts, function($post) {
        return $post['parent'] == NULL;
    });
    $i = 1;
    $thread_posts = array_filter($posts, function($post) use ($thread) {
        return $post['thread'] == $thread['id'];
    });
    $refs = array(
        'date' => 'createdAt',
        'name' => 'author.username',
        'avatar' => 'author.avatar.permalink',
        'website' => 'author.url'
    );
    foreach($thread_posts as $post) {
        $xml = new DomDocument('1.0');
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        $root = $xml->createElement('comment');
        $root = $xml->appendChild($root);
        $body = preg_replace_callback("%<(a[^>]+)>(.*?)</a>%", function($match) {
            return (preg_match("/data-dsq-mention/", $match[1]) ? "@" : "") . $match[2];
        }, $post['message']);
        addChild($xml, $root, 'body', $body);
        foreach ($refs as $key => $value) {
            $parts = explode('.', $value);
            $ref = $post;
            foreach ($parts as $part) {
                $ref = $ref[$part];
            }
            addChild($xml, $root, $key, $ref);
        }
        $name_arr = post_name($thread_posts, $post, $i);
        $fname = $dir . '/' . implode('-', $name_arr) . ".xml";
        $f = fopen($fname, 'w');
        echo $xml->saveXML() . "\n";
        fwrite($f, $xml->saveXML());
        fclose($f);
        chmod($fname, 0664);
        $n = count($name_arr);
        echo str_repeat(" ", 4*$n) . implode('-', $name_arr) . "\n";
        echo str_repeat(" ", 4*$n) . preg_replace("/\n/", "\n" . str_repeat(" ", 4*$n),
                                                  $post['message']) . "\n";
        if ($post['parent'] == null) {
            $i += 1;
        }
    }
}
{% endhighlight %}

Użyłem w tym kodzie funkcji `getSafeThreadName`, która wygeneruje nazwę katalogu, skopiowałem
ją z kodu źródłowego HashOver, aby mieć pewność, że nazwa będzie taka sama (przerobiłem ją tylko z
metody na funkcję, komentarze oryginalne):

{% highlight php startinline=true %}
function reduceDashes ($name)
{
	// Remove multiple dashes
	if (mb_strpos ($name, '--') !== false) {
		$name = preg_replace ('/-{2,}/', '-', $name);
	}

	// Remove leading and trailing dashes
	$name = trim ($name, '-');

	return $name;
}

function getSafeThreadName ($name)
{
    $dashFromThreads = array (
		'<', '>', ':', '"', '/', '\\', '|', '?',
		'&', '!', '*', '.', '=', '_', '+', ' '
	);
	// Replace reserved characters with dashes
	$name = str_replace ($dashFromThreads, '-', $name);

	// Remove multiple/leading/trailing dashes
	$name = reduceDashes ($name);

	return $name;
}
{% endhighlight %}

W XML-u komentarzy dodałem nowy element `avatar`, HashOver korzysta z szyfrowania, aby zapisać
emaile. Można by zapisać email w zaszyfrowanej formie jak je czyta Disqus niestety nie ma do nich dostępu z API (pewnie
ze względu na GDPR/RODO). Dostępne są tylko ścieżki do plików utrzymywane na serwerach Disqus.

Moja modyfikacja biblioteki HashOver wygląda tak (wynik komendy git diff):

{% highlight diff %}
diff --git a/hashover/backend/classes/commentparser.php b/hashover/backend/classes/commentparser.php
index 15b5a2b..bf13c06 100644
--- a/hashover/backend/classes/commentparser.php
+++ b/hashover/backend/classes/commentparser.php
@@ -106,10 +106,16 @@ class CommentParser
 
                // Get avatar icons
                if ($this->setup->iconMode !== 'none') {
+
                        if ($this->setup->iconMode === 'image') {
                                // Get MD5 hash for Gravatar
                                $hash = Misc::getArrayItem ($comment, 'email_hash') ?: '';
-                               $output['avatar'] = $this->avatars->getGravatar ($hash);
+                               $avatar = Misc::getArrayItem ($comment, 'avatar');
+                               if (!empty($avatar)) {
+                                       $output['avatar'] = $avatar;
+                               } else {
+                                       $output['avatar'] = $this->avatars->getGravatar ($hash);
+                               }
                        } else {
                                $output['avatar'] = end ($key_parts);
{% endhighlight %}


Jak już mamy komentarzy to wystarczy je skopiować na serwer do katalogu `hashover/comments/threads/`.
I możemy się cieszyć komentarzami bez reklam, które nikogo nie szpiegują.

Jeśli chciałbyś skasować swoje konto Disqus, może dobrym pomysłem byłoby, także pobranie wszystkich awatarów.

Pod GNU/Linux-em można to zrobić takim poleceniem:

```
gron posts.json | grep author.avatar.permalink | grep -oE 'https?[^"]+' | wget -i -
```

Podpięcie pobranych plików pozostawiam jako ćwiczenie dla czytelnika.

Narzędzie gron można znaleźć na [GitHub-ie](https://github.com/tomnomnom/gron), opisywałem je we wpisie
[5 Bibliotek do przetwarzania obiektów JavaScript i JSON](/2018/02/5-bibliotek-i-narzedzi-do-json-a.html).
