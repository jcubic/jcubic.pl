---
layout: post
title:  "Upload katalogów i plików poprzez Drag & Drop"
date:   2019-06-29 15:21:18+0200
categories:
tags: javascript API upload
description: Wpis o tym jak użyć nowych API, aby wgrać pliki i katalogi na serwer. Upload plików i katalogów jest teraz możliwy poprzez Drag & Drop.
author: jcubic
image:
  url: "/img/office-folders.jpg"
  width: 800
  height: 519
  alt: "Segregatory (Katalogi) na półce"
  attribution: źródło [pxhere](https://pxhere.com/en/photo/1234357), licencja [CC0 Public Domain](https://creativecommons.org/publicdomain/zero/1.0/)
related:
  -
    name: "Dzielenie i Upload Plików na Części"
    url: /2019/08/dzielenie-pliku-na-czesci.html
sitemap:
  lastmod: 2019-08-15 13:39:56+0200
---

Do niedawna możliwy był upload tylko pojedynczych plików. Przeglądarki jednak (Chrome oraz Firefox)
dodały nową funkcje obsługi katalogów.  Funkcja dostępna jest poprzez mechanizm przeciągnij i upuść
(ang. Drag & Drop).

<!-- more -->

## Wstęp

Obsługa katalogów, nie była w żadnym standardzie, ale to nie przeszkodziło przeglądarkom dodania tej
funkcjonalności.  Już po jej dodaniu powstała jednak specyfikacja
[File and Directory Entries API](https://wicg.github.io/entries-api/), stworzona w ramach projektu
[WICG](https://wicg.io/). Jest to projekt
[zapoczątkowany przez W3C](https://www.w3.org/blog/2015/07/wicg/), którego celem jest dodanie
miejsca, gdzie twórcy przeglądarek oraz programiści mogą dyskutować i nowych API. Szczerze mówiąc
dowiedziałem się o tym przedsięwzięciu dopiero szukając informacji o tym API, którego używałem już
od jakiegoś czasu.

Jeśli w swojej aplikacji obsługujesz upload plików, to warto także dodać upload katalogów. Niestety
o ile mi wiadomo działa tylko poprzez Drag & Drop.  **Na końcu** znajdzie się cały kod na
**CodePen**. Demo loguje pliki w konsoli zamiast wywoływać `fetch` i wgrywać pliki na serwer. Aby
mieć prawdziwy upload plików i katalogów, wystarczy tylko podmienić funkcje `upload_file`, na taką
która używa `fetch`, jak w jednym z przykładów w tym artykule.

## Przygotowanie Drag & Drop Plików i Katalogów

Pierwszą rzecz jaką trzeba zrobić, aby obsłużyć zdarzenie Drag & Drop dla plików i katalogów, to
wyłączenie domyślnej obsługi zdarzeń.

{% highlight javascript %}
document.body.addEventListener('drop', function(e) {
    e.preventDefault();
});
document.body.addEventListener('dragover', function(e) {
    e.preventDefault();
});
document.body.addEventListener('dragenter', function(e) {
    e.preventDefault();
});
{% endhighlight %}

Jest to kod potrzebny, aby w ogóle działał Drag & Drop plików. Zdarzenia `dragover` oraz `dragenter`
trzeba po prostu wyłączyć. Natomiast nasz główny kod będzie w zdarzeniu drop.

## Dostęp do plików zdarzenia drop

Aby uzyskać dostęp do plików i katalogów musimy rozważyć dwa przypadki.

* Upload jednego lub listy plików
* Upload jednego lub wielu katalogów

dostęp do plików mamy poprzez `event.dataTransfer.files` lub  `event.target.files` w zależności od
przeglądarki.

Nasz kod może wyglądać tak:

{% highlight javascript %}
const files = Array.from(event.dataTransfer.files || event.target.files || []);
{% endhighlight %}

Obiekt `files`, jest to `FileList`, obiekt tablico podobny, dlatego trzeba go skonwertować na
prawdziwą tablicę, abyśmy mogli go przetworzyć. To API dostępne było już od dawna, może służyć do uploadu
zwykłych plików (przy upuszczaniu jednego lub wielu plików).

Do tego dochodzi nowe API:

{% highlight javascript %}
const items = Array.from(event.dataTransfer.items);
{% endhighlight %}

Item mogą to być pliku lub katalogi. Aby kod był uniwersalny powinno się obsłużyć `files` oraz `items`.
Jest to API dostępne w przeglądarce Google Chrome, przeglądarka FireFox udostępnia API bazujące na
obietnicach (ang. Promises).

Aby wgrać pliki na serwer, trzeba użyć obiektu FormData, wraz z Ajaxem.

{% highlight javascript %}
const form = new FormData();
for (file of files) {
    form.append('files[]', file);
}
const url = 'upload.php';
fetch(url, {
    method: 'post',
    body: form
}).then(function() {
    alert('Upload Done');
});
{% endhighlight %}

Zamiast php może być dowolny inny język, użyte zostało nowe API `fetch`. Warto z niego korzystać, ponieważ
jest to prostsze API niż XHR, [wsparcie jest duże](https://caniuse.com/#feat=fetch), a w przeglądarkach,
które nie zaimplementowały tego API, można użyć polyfill, na przykład minimalistyczny
[unfetch](https://github.com/developit/unfetch).

W przypadku wielu plików, warto także podzielić upload i wgrywać pliki po jednym, ponieważ większość
technologii back-end'owych posiada limity na ilość danych, jaką można przesłać.

## Upload Katalogów

W przypadku katalogów proces jest nieco bardziej skomplikowany, mamy też dwa różne API z prefiksami.
Inne funkcje w przeglądarce Chrome a inne w FireFox.

### Przeglądarka Google Chrome oraz Chromium

W przeglądarce Chrome mamy funkcje o nazwie `webkitGetAsEntry`, która zwraca właściwy obiekt. Musimy
wywołać tą funkcję dla każdego elementu item.

{% highlight javascript %}
if (items.length) {
    if (items[0].webkitGetAsEntry) {
        var entries = [];
        items.forEach(function(item) {
            var entry = item.webkitGetAsEntry();
            if (entry) {
                entries.push(entry);
            }
        });
        var promise = new Promise(function(resolve) {
            (function recur() {
                var entry = entries.shift();
                if (entry) {
                    process_tree(entry, upload_file, 'path').then(recur);
                } else {
                    resolve();
                }
            })();
        });
    }
}
{% endhighlight %}

W powyższym kodzie mamy dwie niezdefiniowane funkcje: `upload_file` oraz `process_tree`. Kod tych
funkcji podany będzie za chwile.

### Przeglądarka FireFox

W przeglądarce FireFox mamy dostęp do funkcji `event.dataTransfer.getFilesAndDirectories`, która
zwraca obietnicę wszystkich plików i katalogów. Kod obsługi wygląda tak:

{% highlight javascript %}
if (event.dataTransfer.getFilesAndDirectories) {
    event.dataTransfer.getFilesAndDirectories().then(function(items) {
        return new Promise(function(resolve) {
            (function recur() {
                var item = items.shift();
                if (entry) {
                    process_tree(item, upload_file, 'path').then(recur);
                } else {
                    resolve();
                }
            })();
        });
    });
}
{% endhighlight %}


## Upload jednego pliku

Pierwsza funkcja, której nam brakuje to zwykły upload, który może wyglądać tak:

{% highlight javascript %}
function upload_file(file, path) {
    const form = new FormData();
    form.append('path', path);
    form.append('file', file);
    const url = 'upload.php';
    return fetch(url, {
        method: 'post',
        body: form,
    });
}
{% endhighlight %}

## Przetwarzanie Katalogów i Plików

Poniżej druga funkcja, której nie pokazałem. Tak wygląda przetwarzanie drzewa katalogów w obu przeglądarkach.

{% highlight javascript %}
function process_tree(tree, process_file, path) {
    return new Promise(function(resolve, reject) {
        function process(entries, callback) {
            entries = entries.slice();
            (function recur() {
                var entry = entries.shift();
                if (entry) {
                    callback(entry).then(recur).catch(reject);
                } else {
                    resolve();
                }
            })();
        }
        function process_and_resolve(file) {
            process_file(file, path).then(function() {
                defered.resolve();
            }).catch(reject);
        }
        function process_entries(entries) {
            process(entries, function(entry) {
                return process_tree(entry, process_file, path + "/" + tree.name);
            });
        }
        if (typeof Directory != 'undefined' && tree instanceof Directory) { // firefox
            tree.getFilesAndDirectories().then(process_entries);
        } else if (typeof File != 'undefined' && tree instanceof File) { // firefox
            process_and_resolve(tree);
        } else if (tree.isFile) { // chrome
            tree.file(process_and_resolve);
        } else if (tree.isDirectory) { // chrome
            var dirReader = tree.createReader();
            dirReader.readEntries(process_entries);
        }
    });
}
{% endhighlight %}

Na koniec jak obiecałem [demo na Codepen](https://codepen.io/jcubic/pen/ZdvdVb?editors=0010).

Serwer powinien odpowiednio przetworzyć poszczególne pliki uwzględniając katalog, także gdy nie istnieje.
Warto tutaj sprawdzać czy nie użyto tzw. directory traversal (czyli czy użytkownik nie użył znaków `..`,
aby wgrać plik w miejsce poza katalogiem docelowym), co byłoby poważnym błędem mogącym dać furtkę
hakerom (a właściwie krakerom). Więcej o tego typu błędach możesz przeczytać w
[artykule o włamywaniu się oraz ochronie stron i aplikacji www](/2018/01/bledy-aplikacji-internetowych.html).


W kolejnym wpisie przedstawię jak podzielić pliki na części, aby wgrać pliki na serwer, gdy ich wielkość
przekroczy limit (np. ten który jest w PHP).

*[WICG]: Web Platform Incubator Community Group
*[API]: Application Programming Interface
