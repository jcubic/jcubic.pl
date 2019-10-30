---
layout: post
title:  "10 Ciekawych Bibliotek w JavaScript"
date:   2018-09-06 21:24:29+0200
categories:
tags:  javascript linki biblioteki
author: jcubic
description: Oto lista 10 ciekawych bibliotek w języku JavaScript. Pierwszy wpis z cyklu listy 10 bibliotek.
image:
  url: "/img/library.jpg"
  alt: "Zdjęcie książek w bibliotece"
  attribution: "źródło: [pixabay.com](https://pixabay.com/pl/biblioteka-ksi%C4%85%C5%BCki-czytelnia-488690/)"
---

W tym wpisie przestawię 10 bibliotek, o których ostatnio usłyszałem i które mnie zainteresowały z jakiegoś powodu.

<!-- more -->


## 1. [Node-Falafel](https://github.com/substack/node-falafel) - Falafel to biblioteka w node, która umożliwia transformacje drzewa (AST) kodu JavaScript. Prawdopodobnie można użyć [Webpack-a](https://webpack.js.org/) albo [Rollup-a](https://rollupjs.org) aby móc użyć jej w przeglądarce.

Użycie jest bardzo proste, jedno wywołanie funkcji:


{% highlight javascript %}
var falafel = require('falafel');

var src = '(' + function () {
    var xs = [ 1, 2, [ 3, 4 ] ];
    var ys = [ 5, 6 ];
    console.dir([ xs, ys ]);
} + ')()';

var output = falafel(src, function (node) {
    if (node.type === 'ArrayExpression') {
        node.update('fn(' + node.source() + ')');
    }
});
console.log(output);
{% endhighlight %}

powyższy kod doda funkcje jako wrapper dla każdego wyrażenia tablicowego.

```
(function () {
    var xs = fn([ 1, 2, fn([ 3, 4 ]) ]);
    var ys = fn([ 5, 6 ]);
    console.dir(fn([ xs, ys ]));
})()
```

Działa też dla JSX poprzez custom-owy parser np. acorn-jsx.

## 2. [Animejs](http://animejs.com/) - Ciekawa biblioteka do animacji.

## 3. [Isomorphic-git](https://isomorphic-git.org/) - Biblioteka dla node i przeglądarek do obsługi gita. Wspominałem o niej w ostatnim wpisie [Server WWW w przeglądarce](/2018/08/serwer-www-w-przegladarce.html).

## 4. [number-flip](https://github.com/gaoryrt/number-flip) - Biblioteka dodaje dość znaną animacje, zmiany liczb lub ciągu znaków z jednej wartości na drugą.

## 5. [jquery.fn](https://github.com/padolsey-archive/jquery.fn/) - Biblioteka zawiera kilka plugin-ów jQuery, najciekawsze są macro, które tworzy plugin jQuery z wywołań jQuery oraz proximity, zdarzenie które odpala się, gdy kursor myszki znajduje się w pobliżu elementu.


## 6. [re-template-tag](http://2ality.com/2017/07/re-template-tag.html) - Ciekawa funkcja jako tag szablonów ciągu znaków (ang. template literals).

{% highlight javascript %}
var regex = re`/foo/u`;
{% endhighlight %}

Dzięki temu, że jest to szablon można korzystać ze zmiennych wewnątrz wyrażenia regularnego. Niestety jest to tylko mała nadbudówka nad natywnym obiektem RegExp.

Ciekawym pomysłem byłoby połączenie tego rozwiązania (które ma tylko kilka linii kodu) razem
z biblioteką [XRegExp](https://github.com/slevithan/xregexp) aby dodać nowe funkcje do wyrażeń regularnych.
I co ciekawe taka biblioteka już istnieje w repozytorium npm, a jest nią [xre](https://www.npmjs.com/package/xre).

## 7. [Svelte](https://svelte.technology/) - Ciekawy framework/kompilator, który generuje minimalistyczny kod, który korzysta tylko z Vanilla JavaScript.

## 8. [JIMP](https://github.com/oliver-moran/jimp) - czyli JavaScript Image Manipulation Program. Odpowiednik ImageMagick w JavaScript według [kodu w npm](https://unpkg.com/jimp) powinien działać także w przeglądarce.

## 9. [Proxymise](https://github.com/kozhevnikov/proxymise) - Biblioteka upraszczająca użycie funkcji, które mają api podobne do `fetch`, gdzie wartość obietnicy posiada funkcje, którą trzeba wywołać.

Dla funkcji `fetch`, kod by wyglądał tak:

{% highlight javascript %}
(async () => {
    const value = await proxymise(fetch('https://jcubic.pl/feed.xml'))
        .text().match(/<title>(.*?)<\/title>/)[1];
    document.body.innerHTML = value;
})();
{% endhighlight %}

gdyby mieć już zdefiniowaną funkcje compose lub pipe, z jakiejś biblioteki lub własną, to można by nadpisać fetch:

{% highlight javascript %}
window.fetch = compose(fetch, proxymise);
// lub
window.fetch = pipe(proxymise, fetch);
{% endhighlight %}

## 10. [Shimport](https://github.com/rich-harris/shimport) - Biblioteka, która umożliwia korzystanie z nowej funkcji w JavaScript, jaką są importy, (moduły ES2015), w każdej przeglądarce.
