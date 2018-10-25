---
layout: post
title:  "Wyszukiwarka plików HTML w PHP i SQLite"
date:   2018-10-25 20:19:56+0200
categories:
tags: php sqlite python jekyll
author: jcubic
description: Jak dodać wyszukiwarkę plików statycznych do bloga w Jekyll lub innego generatora plików statycznych w PHP. Przy pomocy Pythona.
image:
 url: "/img/magnifying-glass.jpg"
 alt: "Szkło powiększające na książce"
 width: 800
 height: 530
---

W tym wpisie przedstawię jak dodać wyszukiwarkę plików statycznych, napisaną w PHP, za pomocą Pythona oraz
[SQLite](https://pl.wikipedia.org/wiki/SQLite).  Ja używam systemu [Jekyll](https://jekyllrb.com/), ale
[statycznych generatorów stron (ang. Static Site Generators) jest cała masa](https://www.staticgen.com/)
rozwiązanie to powinno działać z każdym z nich. O ile serwer, na którym stoi obsługuje PHP. Nie powinno być też
problemu, przepisanie skryptu PHP do innego języka np. Python, Node.js czy Ruby.

<!-- more -->

Pierwszą rzeczą jaką zrobiłem było Google, nie znalazłem nic ciekawego. Następnym krokiem była próba dodania
wyszukiwarka za pomocą Google Custom Search.  Za pomocą ich API dodałem wyszukiwarkę do swojej strony. Ale
wyniki nie były zadowalające, dostałem masę reklam i wyniki z sub-domen, próbowałem w opcjach filtrować, ale
się nie udało.

Dlatego postanowiłem sam napisać wyszukiwarkę. Pierwszy skrypt PHP przeszukiwał drzewo katalogów w
poszukiwaniu plików HTML, ale był bardzo wolny.  Pomyślałem więc, że można by utworzyć bazę danych SQLite,
wrzucić do niej sam tekst ze strony plus url oraz tytuł strony i potem w php przeszukiwać tą bazę, co powinno
być szybsze.

Postanowiłem napisać skrypt w Pythonie. Aby wyciągnąć dane z HTML użyłem biblioteki Beautiful Soup 4, najpierw
musiałem ją zainstalować. Użyłem pip.

```
pip install beautifulsoup4
pip install html5lib
```

Bazując na wpisie
[Extracting text from HTML in Python: a very fast approach](https://rushter.com/blog/python-fast-html-parser/)
napisałem własną funkcje:

{% highlight python %}
def get_data(html):
    """return dictionary with title url and content of the blog post"""
    tree = BeautifulSoup(html, 'html5lib')

    body = tree.body
    if body is None:
        return None

    for tag in body.select('script'):
        tag.decompose()
    for tag in body.select('style'):
        tag.decompose()
    for tag in body.select('figure'):
        tag.decompose()

    text = tree.findAll("div", {"class": "body"})
    if len(text) > 0:
      text = text[0].get_text(separator='\n')
    else:
      text = None
    title = tree.findAll("h2", {"itemprop" : "title"})
    url = tree.findAll("link", {"rel": "canonical"})
    if len(title) > 0:
      title = title[0].get_text()
    else:
      title = None
    if len(url) > 0:
      url = url[0]['href']
    else:
      url = None
    result = {
      "title": title,
      "url": url,
      "text": text
    }
    return result
{% endhighlight %}

Na moim blogu użyłem tagu link do wskazania strony canonical, czyli tej oryginalnej, jest to wskazane z punktu
widzenia SEO.  Dlatego mogłem pobrać url strony bezpośrednio z HTML. Nagłówek strony znajdował się w tagu `h2`, z
atrybutem `itemprop`, też dla wyszukiwarek.  Aby pobrać zawartość strony musiałem
[lekko zmodyfikować html stron](https://github.com/jcubic/jcubic.pl/commit/d5e02ba6bd01cb40901b307f90493f9600dd5781),
musiałem dodać wrapper na samą zawartość, czyli `<div class="body">`. Bez tej zmiany, musiałbym indeksować też
linki zobacz też, linki do Facebook-a, czy link do źródła strony.

Reszta skryptu wygląda tak, tak naprawdę pisałem powyższą funkcje jednocześnie z resztą kodu.

{% highlight python %}
import os, sys, re, sqlite3
from bs4 import BeautifulSoup

if __name__ == '__main__':
  if len(sys.argv) == 2:
    db_file = 'index.db'
    # usunięcie starego pliku
    if os.path.exists(db_file):
      os.remove(db_file)
    conn = sqlite3.connect(db_file)
    c = conn.cursor()
    c.execute('CREATE TABLE page(title text, url text, content text)')
    # traversowanie drzewa katalogów
    for root, dirs, files in os.walk(sys.argv[1]):
      for name in files:
        if name.endswith(".html") and re.search(r"[/\\]20[0-9]{2}", root):
          fname = os.path.join(root, name)
          f = open(fname, "r")
          data = get_data(f.read())
          f.close()
          if data is not None:
            data = (data['title'], data['url'], data['text']
            c.execute('INSERT INTO page VALUES(?, ?, ?)', data))
            print "indexed %s" % data['url']
            sys.stdout.flush() # flush wyświetli text z print na terminalu
    conn.commit() # bez tego nie zapiszą się dane
    conn.close()
{% endhighlight %}

Wyrażenie `re.search(r"[/\\]20[0-9]{2}", root)` filtruje pliki, które nie zaczynają się od 20, czyli te, w
których mam wpisy (np. 2018).  `/\\` jest po to, aby działało pod systemem Windows jak i Linux/MacOSX.

Mając plik bazy SQLite, mogłem napisać skrypt PHP, który by wyszukiwał i wyświetlał wyniki. Oto on

{% highlight php %}
<?php

function mark($query, $str) {
    return preg_replace("%(" . $query . ")%i", '<mark>$1</mark>', $str);
}

if (isset($_GET['q'])) {
  $db = new PDO('sqlite:index.db');
  $stmt = $db->prepare('SELECT * FROM page WHERE content LIKE :var OR title LIKE :var');
  $wildcarded = '%'. $_GET['q'] .'%';
  $stmt->bindParam(':var', $wildcarded); // trzeba użyć zmiennej w tym miejscu
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // bez tego nie można byłoby wyszukać %
  $query = str_replace("%", "\\%", preg_quote($_GET['q']));
  $re = "%(?>\S+\s*){0,10}(" . $query . ")\s*(?>\S+\s*){0,10}%i";
  if (count($data) == 0) {
    echo "<p>Brak wyników</p>";
  } else {
    foreach ($data as $row) {
      if (preg_match($re, $row['content'], $match)) {
        echo '<h3><a href="' . $row['url'] . '">' . mark($query, $row['title']) . '</a></h2>';
        // usunięcie zbędnych znaków interpunkcyjnych oraz białych znaków
        $text = trim($match[0], " \t\n\r\0\x0B,.{}()-");
        echo '<p>' . mark($query, $text) . '</p>';
      }
    }
  }
}

?>
{% endhighlight %}

Następnie należało opakować wyszukiwarkę, w taki sam layout jak cała strona. Okazało się, że Generator plików
statycznych Jekyll, którego używam, nie ma problemów, aby wygenerować plik PHP tak samo jak pliki HTML.

Wystarczył poniższy kod:

{% highlight html %}
---
layout: default
---
<section class="search">
  <div>
    <header>
       <h2>
          Wyszukanie
          "<?= isset($_GET['q']) ? strip_tags($_GET['q']) : '' ?>"
       </h2>
    </header>
<?php /* kod php */ ?>
  </div>
</section>
{% endhighlight %}

Ostatnia część to dodanie formularza w sidebarze:

{% highlight html %}
<div id="search">
    <form action="https://jcubic.pl/search.php">
        <input name="q" placeholder="text do wyszukania"/>
        <input type="submit" value="wyszukaj"/>
    </form>
</div>
{% endhighlight %}

i jego ostylowanie:

{% highlight css %}
#search button {
    background-color: #171f32;
    color: #E9E9E8;
    width: 90px;
}
#search input {
    border: 1px solid #171f32;
    /* 90 button width + 10 padding + 2 border input + 4 border button */
    width: calc(100% - 106px);
}
#search {
    margin-bottom: 20px;
}
#search input, #search button {
    padding: 3px 5px;
    font-size: 1em;
}
{% endhighlight %}

Dodałem też poprawki do wyników, aby wyglądały jak inne strony.

Jedyne z czym miałem problem to wcięcia w HTML. Inne strony są przepuszczane przez
[tidy html5](https://github.com/htacg/tidy-html5), który wypluwa sformatowany kod HTML, o którego ciężko w
narzędziu Jekyll.  Można użyć tidy w php, ale niestety nie da rady, bo musiałbym mieć skrypt tag na początku aby
włączyć buforowanie za pomocą funkcji `ob_start()` (może dodam ją jeszcze, w pliku Makefile, za pomocą sed-a).

I to tyle, możesz przetestować działanie skryptu na stronie, jeśli masz jakieś pytania, albo sugestie odnośnie tego
rozwiązania, zostaw je w komentarzu. Kod możesz znaleźć na GitHubie:

* [index.py](https://github.com/jcubic/jcubic.pl/blob/master/index.py)
* [search.php](https://github.com/jcubic/jcubic.pl/blob/master/search.php)

Plik, z bazą danych SQLite, także jest w repozytorium.
