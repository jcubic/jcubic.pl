---
layout: post
title:  "Trampolina czyli rekurencja bez stosu"
date:   2017-12-25 16:47:31+0100
categories:
tags:  javascript front-end funkcje
author: jcubic
description: Pisanie funkcji rekurencyjnych może być wyzwaniem. Możesz się spotkać z wyjątkiem "Maximum call stack size exceeded" oto sposób na rozwiązenie tego problemu.
related:
  -
    name: Wszystko co powinieneś wiedzieć o Funkcjach w JavaScript
    url: /2014/08/funkcje-w-javascript.html
---

Pisanie funkcji rekurencyjnych może być wyzwaniem. Jeśli musisz napisać taką funkcje, która operuje na dużej ilości danych
(rekurencja wywołuje się wiele razy), przeglądarka może protestować, wyrzucając wyjątek "Maximum call stack size exceeded".
Oto sposób aby temu zaradzić.

<!-- more -->

Wyjątek ten spowodowany jest tym, że wywołanie funkcji tworzy nowa "ramkę" na stosie (czyli specjalnego miejsca w pamięci),
która zawiera argumenty wywołania funkcji.

Jeśli np. masz funkcje rekurencyjną, która akceptuje tablicę i tworzy sumę jej argumentów

{% highlight javascript %}
function sum(arg, ...args) {
    if (args.length == 0) {
        return arg;
    }
    return arg + sum(...args);
}
{% endhighlight %}

to jeśli ją wywołasz z tablicą tysiąca liczb (Liczba elementów może być inna w twoim przypadku) to przeglądarka Google Chrome, 
zwróci wspomniany wcześniej wyjątek.

{% highlight javascript %}
var array = new Array(1000).fill(0).map((_, i) => i + 1);
console.log(sum(...array));
{% endhighlight %}

Poniżej przedstawiam podobną funkcje, wyjątkiem jest to że pierwszy argument przechowuje sumę oraz to, że zwracana jest wywołanie
rekurencyjne opakowane jest w funkcje strzałkową (ang. arrow function):

{% highlight javascript %}
function sum(acc, arg, ...args) {
  acc += arg;
  if (args.length == 0) {
    return acc;
  }
  return () => sum(acc, ...args);
}
{% endhighlight %}

Aby ta funkcja zadziałała potrzebujemy specjalnej funkcji, która "odwinie" funkcje sum. Wygląda ona tak:

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

Można też stworzyć funnkcje trampoline_sum bezpośrednio przekazują wyrażenie funkcujne z nazwą:

{% highlight javascript %}
var trampoline_sum = trampoline(ffunction sum(acc, arg, ...args) {
  acc += arg;
  if (args.length == 0) {
    return acc;
  }
  return () => sum(acc, ...args);
});
{% endhighlight %}

Zastanawiasz się może, poco zawracać sobie głowę trampoliną, kiedy możesz po prostu użyć zwykłej pętli.
Czasami rekurencja jest prostszym albo nawet jedynym rozwiązaniem. Istnieją np. gotowe rekurencyjne algorytmy,
które by było bardzo trudno zastąpić pętlami np. przechodzenie drzewa lub grafu.

