---
layout: post
title:  "5 Bibliotek do przetwarzania obiektów JavaScript i JSON"
date:   2018-02-18 11:42:07+0100
categories:
tags:  json biblioteki javascript linki
author: jcubic
description: 5 Bibliotek i narzędzi operujących na obiektach JavaScript i JSON.
image:
  url: "/img/lego-bricks.jpg"
  alt: "Klocki Lego"
---

JSON to standard opracowany przez Douglasa Crockforda, na początku roku 2000, służący do zapisu
obiektów w postaci tekstu. Dzisiaj trudno sobie wyobrazić pisanie aplikacji internetowych
bez tego formatu. W tym wpisie przedstawię 5 ciekawych bibliotek i narzędzi, które operują
na obiektach JSON lub obiektach JavaScript.

<!-- more -->

### 1. go-sql

[go-sql](https://github.com/timtian/qo-sql) to biblioteka, która korzysta
z lodash. Przetwarza ona obiekty JavaScript za pomocą języka SQL. Ciekawą funkcją jest dodatek
do biblioteki Babel (kompilatora/transpilera nowoczesnych wersji ECMAScript), który konwertuje, kod korzystający z zapytań SQL, do postaci funkcji korzystających z lodash.

Przykład wywołania jako biblioteki:

{% highlight javascript %}
var res = qos.exec("select (id + 1) as index, name  from ${testData} where id > ${minid} and type = 'C'", {
    testData : testData,
    minid : 2
});
{% endhighlight %}

### 2. Narzędzie gron

[gron](https://github.com/TomNomNom/gron) to narzędzie wiersza poleceń, które konwertuje
JSON-a do postaci tekstu, który można następnie przetwarzać za pomocą takich narzędzi jak
grep czy sed. Następnie można, na takim tekście, wykonać operacje ungron, która skonwertuje
obiekt z powrotem do postaci JSON-a. Dodatkową funkcją biblioteki, jest możliwość pobierania
JSON-a z zewnętrznego źródła poprzez URL.

Przykład:

{% highlight bash %}
gron "https://api.github.com/repos/tomnomnom/gron/commits?per_page=1" | fgrep "commit.author"
json[0].commit.author = {};
json[0].commit.author.date = "2016-07-02T10:51:21Z";
json[0].commit.author.email = "mail@tomnomnom.com";
json[0].commit.author.name = "Tom Hudson";
{% endhighlight %}

### 3. json5

[json5](https://github.com/json5/json5) jest to biblioteka, która rozszerza możliwości JSON-a.
Dodaje takie funkcje jak np.

* klucze bez cudzysłowów
* ciągi znaków ograniczone za pomocą pojedynczego cudzysłowu
* wielowierszowe ciągi znaków
* komentarze
* przecinek za ostatnim elementem


### 4. json-dry

[json-dry](https://github.com/skerit/json-dry) to jeszcze jedna biblioteka rozszerzająca
możliwości JSON-a, jest moim zdaniem o wiele bardziej użyteczna. Dodaje takie funkcje jak,
zapisywanie i odczytywanie:

* wyrażeń regularnych
* dat
* obiektów które mają cykle
* własnych klas


### 5. Edytor JSON-a

[jsoneditor](https://github.com/josdejong/jsoneditor) to ciekawy projekt który umożliwia podgląd struktury JSON-a w postaci drzewa. Na oficjalnej stronie można znaleźć [demo edytora](http://jsoneditoronline.org/).
