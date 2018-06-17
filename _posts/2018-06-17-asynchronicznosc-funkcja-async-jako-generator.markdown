---
layout: post
title:  "Asynchroniczność cz. 4: Funkcja async jako generator"
date:   2018-06-17 10:54:51+0200
categories:
description: Wpis opisujący mechanizm zapisu funkcji asynchronicznej jako generatora, podobnie jak to robi narzędzie Babel.
tags: javascript es2018 es6 asynchroniczność
author: jcubic
image:
  url: "/img/golf-ball.jpg"
  alt: "Piłka do golfa na trawie"
  attribution: Autor [Mabel Amber](https://www.pexels.com/@mabelamber), źródło [pexels.com](https://www.pexels.com/photo/golf-golf-ball-golf-course-golfing-141884/) licencja [CC0](https://creativecommons.org/publicdomain/zero/1.0/)
related:
  -
    name: "Asynchroniczność cz. 1: Obietnice"
    url: /2018/05/asynchronicznosc-obietnice.html
  -
    name: "Asynchroniczność cz. 2: Async/Await"
    url: /2018/05/asynchronicznosc-async-await.html
  -
    name: "Asynchroniczność cz. 3: Iteratory i Generatory Asynchroniczne"
    url: /2018/06/asynchronicznosc-iteratory-i-generatory-asynchroniczne.html
  -
    name: "Iteratory i Generatory"
    url: /2018/06/iteratory-i-generatory.html
---


Funkcje `async` oraz słowo kluczowe `await` są częścią es8 (es2017). Nie są dostępne we wszystkich przeglądarkach,
chociaż ich wsparcie jest bardzo duże. Jeśli jesteś zainteresowany w jaki sposób Babel konwertuje `async..await`, aby
przeglądarki, które ich nie obsługują mogły uruchomić ten kod, to ten wpis jest dla Ciebie. Kod ten wygląda jak
jeden do jeden dlatego pomyślałem, że warto o tym napisać.

<!-- more -->

Generatory to część es6 czyli es2015 i właśnie za ich pomocą Babel tworzy funkcje, które w oryginale używają słowa
kluczowego `async`. Udostępnia też kod tzw. regenerator dla kodu, który korzysta z generatorów (jeśli nie zmusimy
go aby tego nie robił), ale my nie będziemy się nim tutaj zajmować.

Poniżej prosta funkcja asynchroniczna, korzystająca z `async..await`, która zwraca tytuł pliku RSS, dla strony
Głównie JavaScript (blog używa [CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS), więc można użyć tego
kodu też na innych stronach np. z CodePen, link na końcu):

{% highlight javascript %}
async function title() {
    var res = await fetch('https://jcubic.pl/feed.xml');
    var text = await res.text();
    var parser = new DOMParser();
    var xmlDoc = parser.parseFromString(text, "text/xml");
    return xmlDoc.querySelector('title').innerHTML;
}
{% endhighlight %}

Ta sama funkcja jako generator wygląda tak:

{% highlight javascript %}
function* title() {
    var res = yield fetch('https://jcubic.pl/feed.xml');
    var text = yield res.text();
    var parser = new DOMParser();
    var xmlDoc = parser.parseFromString(text, "text/xml");
    return xmlDoc.querySelector('title').innerHTML;
}
{% endhighlight %}

Jedyna różnica to gwiazdka po słowie `function` zamiast słowa `async` oraz słowo `yield` zamiast `await`.

Aby ten kod zadziałał, możemy wykorzystać ciekawą właściwość iteratorów (czyli niskopoziomowego api, które kryje są
za generatorami), a mianowicie możemy przekazać wartość, do następnego wywołania funkcji `next` iteratora i ta
wartość zostanie zwrócona przez słowo kluczowe `yield`, kiedy iterator wznowi swoje działanie.

Funkcja która przetworzy generator i zwróci obietnice wartości, jaką zwraca oryginalna funkcja, wygląda tak:

{% highlight javascript %}
// funkcja zwraca true dla zwykłych obietnic jak i obiektów,
// które wyglądają jak obietnice, jak np. jQuery Defered
function is_promise(value) {
    return value instanceof Promise || (typeof value === 'object' && typeof value.then === 'function');
}

function unwind(gen) {
    // pobieramy iterator z generatora
    var iterator = gen()[Symbol.iterator]();
    return new Promise(function(resolve) {
        (function next(value) {
            // przekazujemy poprzednią wartość do następnego next
            value = iterator.next(value);
            if (!value.done) {
                if (is_promise(value.value)) {
                    // wartość value z funkcji next to będzie już wartość obietnicy,
                    // a nie sama obietnica
                    value.value.then(next);
                } else {
                    // zwykła wartość - raczej nie użyjemy async dla takich wartości
                    // dlatego nie powinno wystąpić ale nic nie stoi na przeszkodzie
                    // aby wywołac var answer = await 42;
                    next(value.value);
                }
            } else {
                // nasza obietnica dostanie wartość, która zostaje zwrócona
                // przez return oryginalnej funkcji
                resolve(value.value);
            }
        })();
    });
}
{% endhighlight %}

A tutaj jak wywołać funkcje unwind wraz z generatorem:

{% highlight javascript %}
unwind(title).then((title) => console.log(title));
{% endhighlight %}

Na koniec [moje demko na CodePen](https://codepen.io/jcubic/pen/oyBrZW?editors=0011), które zawiera kod z
tego wpisu.
