---
layout: post
title:  "Zakres blokowy w JavaScript"
date:   2018-09-28 21:52:29+0200
categories:
tags: javascript es6
author: jcubic
description: Wpis opisujący zakres blokowy, czyli słowa kluczowe let oraz const w ES6 (ES2015), który zastępuje w większości przypadków IIFE.
image:
 url: "/img/let-const.png"
 alt: "Grafika ze słowem kluczowe let oraz const"
sitemap:
  lastmod: 2021-01-20 09:44:25+0100
---

Ponieważ wpis o [funkcjach w JavaScript](/2014/08/funkcje-w-javascript.html) na moim blogu
ma wysoką pozycje w Google, a opisuje jeden z celi korzystania z IIFE, czyli
natychmiastowo-wywoływanych wyrażeń funkcyjnych w celu tworzenia zakresu zmiennych.
Postanowiłem opisać krótko jak działa `const` oraz `let`, czyli zakres blokowy w
JavaScript w wersji ES6 (czyli poprawnie ES2015).


<!-- more -->

Do tej pory (przed ES6) aby dodać nowy zakres zmiennych należało stosować funkcje
anonimowe czyli IIFE. Z powodu tego, że blok nie tworzył nowej zmiennej, mieliśmy taki kod,
który nawiasem mówiąc, może pojawić się na rozmowie kwalifikacyjnej na stanowisko
Front-End developera.  (więcej pytań we wpisie
[5 pytań na rozmowę kwalifikacyjną z języka JavaScript](/2017/09/pytania-na-rozmowe-kwalifikacyjna-z-javascript.html)).

{% highlight javascript %}
for (var i = 0; i <= 9; i++) {
    setTimeout(function() {
        console.log(i);
    }, i*1000);
}
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
{% endhighlight %}

Jest to spowodowane tym, że `var` wewnątrz `for` jest jeden na całą funkcje (jest jedna referencja)
i po skończeniu działa pętli zawiera wartość 10.

Rozwiązaniem tego problemu do tej pory było tylko IIFE:

{% highlight javascript %}
for (var i = 0; i <= 9; i++) {
    (function(i) {
        setTimeout(function() {
            console.log(i);
        }, i*1000);
    })(i);
}
// ==> 0
// ==> 1
// ==> 2
// ==> 3
// ==> 4
// ==> 5
// ==> 6
// ==> 7
// ==> 8
// ==> 9
{% endhighlight %}


ale wraz z wejściem ES6 (ES2015), można zmienną zadeklarować jako `let`, lub w przypadku
pętli `fo..in` jako `const`. Zwykły `for` nie zadziała z `const` ponieważ musisz zmienić
jego wartość w każdej iteracji pętli. Natomiast `for..in` oraz nowy `for..of` tworzy nową
zmienną w każdej iteracji pętli.

Istnieje też możliwość deklaracji zakresu zmiennych wewnątrz pustego bloku. np.:

{% highlight javascript %}
{
   let x = 10;
   console.log(x + 10);
}
console.log(x);
{% endhighlight %}

Ten kod zwróci wyjątek `ReferenceError` w drugiej linijce `console.log`, ponieważ zakres zmiennej
`x` się skończył i jest nie zdefiniowana.

Jedna rzecz o jakiej trzeba pamiętać jest to, że nie można użyć zmiennej przed
deklaracją. W przypadku `var` zmienną można było użyć przed jej deklaracji jeśli była
umieszczona w tej samej funkcji, w tym samym zakresie zmiennej `var` (ang. scope) z powodu
mechanizmu nazywanego z angielskiego hoisting.

{% highlight javascript %}

console.log(x);
var x = 10;
console.log(x);
{% endhighlight %}

Powyższy kod nie zwróci błędu tylko wyświetli `undefined` oraz `10`. W przypadku `const` oraz `let`.

{% highlight javascript %}
{
   console.log(x + 10);
   let x = 10;
}
{% endhighlight %}

Zwrócony zostanie wyjątek `ReferenceError`, ponieważ zmienne `let` oraz `const` znajdują się w tzw.
tymczasowej martwej strefie (ang. temporal dead zone). Podobno jest to definicja wzięta ze specyfikacji
ES6 ale mi nie udało się tego potwierdzić (jest skrót TDZ w specyfikacji ale znaczy co innego).

Jeśli znasz skąd się wzięła ta nazwa napisz w komentarzu.

Różnica miedzy `const` a `let` jest taka, że do `let` można przypisać nową wartość. Możesz myśleć, że
`const` jest to po prostu stała czyli zmienna, która nie może zmienić swojej wartości, ale to nie prawda.

{% highlight javascript %}
const arr = [10];
arr.push(20);
{% endhighlight %}

jest to całkiem poprawny kod, który nie zmienia wartości, tylko modyfikuje obiekt, na który wskazuje
zmienna. `const` działa jak stała referencja, której nie można zmienić. Aby uzyskać stałą należałoby użyć:

{% highlight javascript %}
const arr = Object.freeze([10]);
arr.push(20);
{% endhighlight %}

ten kod zwróci wyjątek `TypeError` ponieważ `arr` jest tylko do odczytu.

*[IIFE]: Immediately-Invoked Function Expression
