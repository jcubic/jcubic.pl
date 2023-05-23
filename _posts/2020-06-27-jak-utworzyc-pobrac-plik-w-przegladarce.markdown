---
layout: post
title:  "Jak utworzyć plik dynamicznie w przeglądarce"
date:   2020-06-27 18:46:50+0200
categories:
tags: pliki javascript
author: jcubic
description: Wpis o generowaniu i pobieraniu plików w przeglądarce.
image:
 url: "/img/files-folders.jpg"
 alt: "Zdjęcie kartoteki folderów i plików"
 width: 800
 height: 450
 attribution: źródło [wallpaperflare.com](https://www.wallpaperflare.com/blue-metal-folder-cases-organization-register-files-office-wallpaper-zfetf); licencja [Domena Publiczna](https://creativecommons.org/licenses/publicdomain/)
---


W tym krótkim wpisie przedstawię jak utworzyć plik dynamicznie w przeglądarce, a następnie
pobrać go na dysk.

<!-- more -->

Pierwszą rzeczą jaką trzeba zrobić, to utworzenie obiektu Blob z naszą zawartością.
W przypadku plików tekstowych jest to najprostsze, bo wystarczy wywołać:

{% highlight javascript %}
var file = new Blob([string], { type: 'text/plain' });
{% endhighlight %}

Ważne jest, aby pamiętać, że trzeba przekazać tablicę z naszym łańcuchem znaków,
inaczej to nie zadziała.

Następnie trzeba utworzyć URL, będzie on potrzebny za chwilę:

{% highlight javascript %}
var url = URL.createObjectURL(file);
{% endhighlight %}

`createObjectURL` jest to bardzo fajne API, ponieważ nasz blob jest w pamięci, a funkcja utworzy
specjalny link do tego miejsca w pamięci (tak przynajmniej mi się wydaje że to działa)
w każdym razie dostaniemy specjalny URL, który zawiera hash naszego bloba. Dzięki temu
można pobrać bardzo duże pliki i nie trzeba się przejmować, że przekroczymy limit długości URL-a.
Jeśli nie mamy tej funkcji trzeba użyć starego API czyli data URL, jest to sposób zawarcia
danych bezpośrednio w URL. Więcej na temat data URL, na stronie
[MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/Data_URIs).

Teraz wystarczy pobrać ten plik na dysk. Do tego celu wystarczy utworzyć link, dodać go do
strony, kliknąć a potem można go usunąć. Wykona się to tak szybko, że nawet nie będzie widać, że
coś zostało dodane do strony.

{% highlight javascript %}
function download(url, filename) {
    var link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
{% endhighlight %}

I to wszystko co trzeba zrobić.

Poniżej wsparcie przeglądarek dla użytych API:

* [createObjectURL](https://caniuse.com/#feat=mdn-api_url_createobjecturl)
* [atrybut download](https://caniuse.com/#feat=download)
* [konstruktor Blob](https://caniuse.com/#feat=blobbuilder)

Istnieje także bardziej zaawansowany sposób tworzenia plików, które można otworzyć
poprzez pasek adresu przeglądarki jak normalne pliki. Przeczytasz o tym we wpisie:
[Server WWW w przeglądarce](/2018/08/serwer-www-w-przegladarce.html).
