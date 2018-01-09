---
layout: post
title:  "Trampolina czyli rekurencja bez stosu"
date:   2018-01-09 18:02:58+0100
categories:
tags:  javascript funkcje
author: jcubic
description: Pisanie funkcji rekurencyjnych może być wyzwaniem. Możesz się spotkać z wyjątkiem "Maximum call stack size exceeded" oto sposób na rozwiązanie tego problemu.
related:
  -
    name: Wszystko co powinieneś wiedzieć o funkcjach w JavaScript
    url: /2014/08/funkcje-w-javascript.html
image:
  url: /img/trampoline.jpg
  alt: Człowiek skaczący na trampolinie
---

Pisanie funkcji rekurencyjnych może być wyzwaniem. Jeśli musisz napisać taką funkcje, która operuje na dużej ilości danych
(rekurencja wywołuje się wiele razy), przeglądarka może protestować, wyrzucając wyjątek "Maximum call stack size exceeded".
Oto sposób aby temu zaradzić.

<!-- more -->

Wyjątek ten spowodowany jest tym, że wywołanie funkcji tworzy nowa "ramkę" na stosie (czyli specjalnego miejsca w pamięci),
która zawiera argumenty wywołania funkcji. Jeśli wywołujesz funkcje w funkcji, jak przy rekurencji, to ramka z poprzedniego
wywołania funkcji nie jest zwalniana, czyli ilość zużytej pamięci rośnie liniowo.

Jeśli np. masz funkcje rekurencyjną, która tworzy sumę jej argumentów:

{% highlight javascript %}
function sum(arg, ...args) {
    if (args.length == 0) {
        return arg;
    }
    return arg + sum(...args);
}
{% endhighlight %}

to jeśli ją wywołasz, używając tablicy tysiąca liczb (liczba elementów może być inna w twoim przypadku) to przeglądarka,
przynajmniej Google Chrome, zwróci wspomniany wcześniej wyjątek.

{% highlight javascript %}
var array = new Array(1000).fill(0).map((_, i) => i + 1);
console.log(sum(...array));
{% endhighlight %}

Jest to spowodowane tym, że każdy element tablicy będzie musiał być umieszczony na stosie, Będzie to tak jakby wywoływać
funkcje sum z tysiącem argumentów. W przypadku gdy przekażemy do funkcji tablicę liczb, będzie musiało być o wiele więcej
wywołań rekurencyjnych, aby przeglądarka wyrzuciła wyjątek.


## Rozwiązanie

Poniżej przedstawiam podobną funkcję, wyjątkiem jest to że pierwszy argument przechowuje sumę (nie powinno to mieć znaczenia)
oraz to, że zwracane wywołanie rekurencyjne, opakowane jest w funkcje strzałkową z ES6 (ang. arrow function):

{% highlight javascript %}
function sum(acc, arg, ...args) {
  acc += arg;
  if (args.length == 0) {
    return acc;
  }
  return () => sum(acc, ...args);
}
{% endhighlight %}

Aby ta funkcja zadziałała potrzebujemy specjalnej funkcji, która będzie "odwijała" funkcje sum w pętli. Wygląda ona tak:

{% highlight javascript %}
function trampoline(fn) {
  return function(...args) {
    var result = fn(...args);
    while (typeof result == 'function') {
      result = result();
    }
    return result;
  };
}
{% endhighlight %}

Jest to [funkcja wyższego rzędu](/2014/08/funkcje-w-javascript.html), do której możemy przekazać funkcje sum z poprzedniego
przykładu:

{% highlight javascript %}
var trampoline_sum = trampoline(sum);
{% endhighlight %}

Można też stworzyć funkcje trampoline_sum bezpośrednio przekazują wyrażenie funkcyjne z nazwą:

{% highlight javascript %}
var trampoline_sum = trampoline(function sum(acc, arg, ...args) {
  acc += arg;
  if (args.length == 0) {
    return acc;
  }
  return () => sum(acc, ...args);
});
{% endhighlight %}

Dzięki trampolinie i "zawijaniu" wywołania rekurencyjnego w funkcje (można też użyć zwykłej funkcji, nie tylko strzałkowej),
za każdym razem na stosie będą argumenty tylko z jednego wywołania.

Zastanawiasz się może, po co zawracać sobie głowę trampoliną, kiedy możesz po prostu użyć zwykłej pętli, albo `Array::reduce`.
Czasami rekurencja jest prostszym, albo nawet jedynym rozwiązaniem. Istnieją np. gotowe rekurencyjne algorytmy, które by było
trudno zastąpić pętlami np. przechodzenie drzewa lub grafu.

Możesz przetestować powyższe funkcje w tym [demo](https://codepen.io/jcubic/pen/VymROK?editors=0011).
