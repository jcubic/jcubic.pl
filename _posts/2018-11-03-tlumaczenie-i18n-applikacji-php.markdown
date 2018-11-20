---
layout: post
title:  "Tłumaczenie aplikacji w PHP za pomocą gettext"
date:   2018-11-03 21:58:24+0100
categories:
tags: php i18n
author: jcubic
description: Internacjonalizacja, czyli po angielsku w skrócie i18n, to dodawanie do aplikacji obsługi wielu języków. W tym wpisie przedstawię jak dodać ją do PHP.
image:
 url: "/img/translation.png"
 alt: "dwa znaki chiński oraz litera A ze strzałkami"
 width: 800
 height: 500
 attribution: "Oryginał [Ætoms](https://commons.wikimedia.org/wiki/User:%C3%86toms), źródło [Wikimedia Commons](https://commons.wikimedia.org/wiki/File:%C3%86toms_-_Translation.svg) licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/4.0/deed.en)"
---

Internacjonalizacja, czyli po angielsku Internationalization, w skrócie i18n to dodawanie do aplikacji obsługi
wielu języków. Ostatnio szukając czegoś na temat gettext, czyli biblioteki do obsługi wielu języków znalazłem
[artykuł na Wikipedii](https://pl.wikipedia.org/wiki/GNU_gettext). Był on niekompletny, ponieważ nie zawierał
opisu jak dodać [tłumaczenie z liczebnikami](/2018/08/rzeczownik-przy-liczebniku.html), oczywiście edytowałem
wpis i dodałem odpowiednie informacje. W związku z tym postanowiłem napisać wpis o getext w PHP, ponieważ
w tym języku ostatnio pisałem aplikacje.

<!-- more -->

Pierwsza wersja biblioteki gettext została napisana w języku C i jest częścią [projektu GNU](https://guu.org).
Czyli wolnej wersji unixa, dzięki której mamy dzisiaj dystrybucje Linuxa, które często nazywane są GNU/Linux.
Ponieważ jest to system GNU + jądro Linux.

Gettext dostępny jest w wielu językach programowania, nawet w JavaScript-cie, przykładem jest np.
[angular-gettext](https://github.com/rubenv/angular-gettext) do biblioteki AngularJS.

Gettext operuje na plikach z rozszerzeniem `.po`, które zawierają tłumaczenie. Narzędzie zawiera program,
który potrafi wyciągnąć z kodu źródłowego różnych języków wywołanie funkcji `_`, która jest aliasem funkcji `gettext`.
Nie będę jednak opisywał jak generować plik dla oryginalnego języka. Jeśli jesteś tym zainteresowany możesz
przeczytać o tym w [dokumentacji gettext](https://www.gnu.org/software/gettext/).

### Plik z tłumaczeniem

Oto przykład składni pliki `.po`:

```
msgid ""
msgstr ""
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Language: pl_PL\n"
"Plural-Forms: nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);\n"

msgid "week"
msgid_plural "weeks"
msgstr[0] "tydzień"
msgstr[1] "tygodnie"
msgstr[2] "tygodni"
```

W powyższym pliku mamy nagłówek, który zawiera jak w języku polskim wygląda liczba mnoga. Mamy 3 warianty oraz
wyrażenie, dla których liczb ma być jaki wariant. Dla różnych języków są różne warianty np. dla języka
angielskiego mamy tylko dwa z końcówkę s na końcu lub bez. `msgid` to słowo do tłumaczenia w języku
angielskim. Natomiast `msgid_plural` to liczba mnoga. W przypadku zwykłych słów, których nie używamy przy
liczebnikach np. jeśli mamy:

{% highlight html %}
<label>user</label>
{% endhighlight %}

I chcemy go przetłumaczyć, aby mieć dwa języku Angielski i Polski to wystarczy:

```
msgid "user"
msgstr "użytkownik"
```

Plik .po z tłumaczeniem powien znajdować się w katalogu `locale/pl_PL/LC_MESSAGES/` zamiast `locale` może
być inny katalog np. `lang` albo `i18n`, ale wewnątrz muszą być katalogi dla poszczególnych języków.
Najlepiej jak język jest w formacie locale dla standardu POSIX, czyli tego w systemach GNU/Linux. Nie próbowałem
innego formatu i nie wiem czy działa.

### Kompilacja

Jak mamy już przygotowany plik `.po` musimy wygenerować jego binarny odpowiednik, czyli plik `.mo`. Do tego
służy program `msgfmt`, aby go wywołać należy wykonać takie polecenie, testowałem tylko z systemem GNU/Linux.


```
msgfmt site.po -o site.mo
```

jeśli znajdujemy się w którymś z katalogów `LC_MESSAGES` i jest w nim plik `site.po` wygenerowany zostanie plik
`site.mo`.

### Interfejs PHP

Teraz jak już mamy plik `.mo` możemy skonfigurować PHP, aby można było używać tej biblioteki.
Kod inicjujący wygląda tak.

Najpierw musimy zmienić zmienne środowiskowe:

{% highlight php startinline=true %}
$lang = 'pl_PL';
$locale = $lang . ".utf8";
putenv("LC_ALL=$lang");
setlocale(LC_ALL, $lang);
{% endhighlight %}

Następnie ładujemy pliki, ja używam funkcji, która załaduje wszystkie dla danego języka. Ale można się ograniczyć
tylko do tego, którego aktualnie będziemy używać.

{% highlight php startinline=true %}
function load_gettext_domains($root, $lang) {
    if (!preg_match("%" . DIRECTORY_SEPARATOR . "$%", $root)) {
        $root .= DIRECTORY_SEPARATOR;
    }
    $path = $root . DIRECTORY_SEPARATOR .
            $lang . DIRECTORY_SEPARATOR . "LC_MESSAGES";
    if (file_exists($path)) {
        foreach (scandir($path) as $file) {
            if (preg_match("/(.*)\.mo$/", $file, $match)) {
                bindtextdomain($match[1], $root);
            }
        }
    }
}
{% endhighlight %}

I wywołuję ją w ten sposób:

{% highlight php startinline=true %}
$root = __DIR__ . DIRECTORY_SEPARATOR
load_gettext_domains($root . "locale", $lang);
{% endhighlight %}


Jak mamy już załadowane wszystkie pliki (ja mam to w konstruktorze głównej klasy aplikacji) w ruterze aplikacji
można określić, którego pliku będziemy używali. Robi się to w ten sposób:

{% highlight php startinline=true %}
textdomain("site")
{% endhighlight %}

Dzięki temu możesz np. mieć jeden plik `admin.po` z tłumaczeniami dla panelu administracyjnego, a drugi do
strony głównej. Możesz mieć też mieć tak, że każdy url korzysta z innego pliku. Nic nie szkodzi jednak
na przeszkodzie, aby mieć jeden plik z wszystkimi tłumaczeniami, i aby wywołanie `textdomain` także było
w konstruktorze, jeśli takowego używasz.

### Tłumaczenie ciągów znaków

Teraz główna część czyli tłumaczenie stringów. Aby przetłumaczyć zwykły tekst można użyć funkcji `_` np.

{% highlight php startinline=true %}
echo _("week");
{% endhighlight %}

Funkcja `_` jest to alias do funkcji `gettext`, drugą użyteczną funkcją jest `ngettext`, której użycie jest
trochę bardziej skomplikowane. Przekazuje się do niej wersje liczby pojedynczej mnogiej oraz liczbę:

{% highlight php startinline=true %}
$n = 5;
echo "to było $n " . ngettext("week", "weeks", $n) . " temu";
{% endhighlight %}

Wewnątrz tłumaczonych znaków można też używać znaku `%s`, który zastępuje jakąś zmienną.
Można ich używać z [funkcją sprintf](https://secure.php.net/manual/pl/function.sprintf.php), np.:

{% highlight php startinline=true %}
$n = 5;
echo sprinf(ngettext("it was %s week ago", "it was %s weeks ago", $n), $n);
{% endhighlight %}

Podwójne wywołanie można zastąpić funkcją:

{% highlight php startinline=true %}
function _n($single, $plural, $count) {
    return sprinf(ngettext($single, $plural, $count), $count);
}
{% endhighlight %}

Dzięki temu, że funkcja `sprintf` nie zwraca błędu, gdy jest argument, a nie ma znacznika w ciągu znaków, można tej
funkcji używać także zamiast zwykłego `ngettext`. Ale już gdy trzeba dodatkowo np. wstawić imię użytkownika, trzeba
będzie użyć kombinacji obu funkcji.

Natomiast nasz plik `.po` będzie wyglądał tak:

```
msgid "it was %s week ago"
msgid_plural "it was %s weeks ago"
msgstr[0] "to było %s tydzień temu"
msgstr[1] "to było %s tygodni temu"
msgstr[2] "to było %s tygodnie temu"
```

### Przeładowywanie pliku po kompilacji

Jedyny problem z gettext w PHP, jest to, że po wygenerowaniu pliku `.mo` należy zrestartować serwer apache.
Nie wiem jak jest z innymi serwerami. Jeśli chcesz się dowiedzieć jak wyczyścić cache bez przeładowywania
strony możesz sprawdzić na StackOverflow odpowiedź na pytanie:
[How to clear php's gettext cache without restart Apache nor change domain?](https://stackoverflow.com/a/13629035/387194).

W moim przypadku dodanie wywołanie `clearstatcache();` przed inicjacją gettext pomogło. Ale czasami,
jak odświeża się cache po kompilacji, serwer zwraca 503, ale może to nie ma związku.

### Podsumowanie

Gettext jest bardzo użytecznym narzędziem. A po skonfigurowaniu jego używanie jest bardzo proste.
Przy generowaniu plików z kodu źródłowego, czego jeszcze nie robiłem, można pisać całą aplikacje w języku
angielskim, dodając wywołania `_` oraz `sprintf(ngettext(` i pod po jakimś skończonym etapie, generować plik `.po`.
Można też rozdzielić pracę programistyczną od tłumaczenia. Może to robić inna osoba, np. po zakończeniu pisania
kolejnej wersji. Tak to np. jest zrealizowane w programie Open Source [Claws-Mail](https://www.claws-mail.org/),
gdzie jestem tłumaczem na [język Polski](https://www.claws-mail.org/i18n.php?section=projects).
