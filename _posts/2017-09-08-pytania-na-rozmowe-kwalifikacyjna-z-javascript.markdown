---
layout: post
title:  "5 pytań na rozmowę kwalifikacyjną z języka JavaScript"
date:   2017-09-08 22:56:53+0200
categories:
tags:  javascript praca
author: jcubic
description: Oto 5 pytań jakie bym zdał na rozmowie rekrutacyjnej z języka JavaScript
related:
  -
    name: "15 Pytań na rozmowę rekrutacyjną z React.js"
    url: "/2018/10/pytania-rekrutacyjne-z-react.js.html"
sitemap:
  lastmod: 2018-10-04 19:36:59+0200
---

Kilka dni temu zostałem poproszony o przygotowanie 3 pytań, dla kandydata na programistę Full Stack, z języka
JavaScript. Przygotowałem 4 i potem dodałem jeszcze jedno. Oto one.

<!-- more -->

## Co to są domknięcia (ang. closures)?

W skrócie, domknięcia są to funkcje które mają dostęp do środowiska, w którym zostały zdefiniowane.

Popatrz na poniższy kod:

{% highlight javascript %}

function a() {
  var b = 10;
  return function() {
     return b;
  };
}
c = a();
c()
// -> 10
{% endhighlight %}

Wywołanie funkcji `c()` zwróci liczbę 10, mimo że zakres dla zmiennej b się skończył, kiedy skończyła się funkcja a, zmienna b jest nadal dostępna wewnątrz funkcji wewnętrznej, dzięki czemuś o nazwie domknięcia.

## Co to jest hoisting?

Deklaracje funkcji i zmiennych są przenoszone na początek funkcji, w której zostały zdefiniowane, ale już przypisanie nie np:

{% highlight javascript %}
var x = 5;
function foo() {
   …
   console.log(x);
   var x = 10;
}
foo();
{% endhighlight %}

Jest konwertowane przez engine JavaScript na

{% highlight javascript %}
var x = 5;
function foo() {
   var x;
   …
   console.log(x);
   x = 10;
}
foo();
{% endhighlight %}

Dlatego powyższe wywołanie `console.log` nie zwróci wyjątku `ReferenceError`, ani nie wyświetli wartości 5, tylko wypisze wartość `undefined`, ponieważ zmienna bez przypisania ma właśnie wartość `undefined`.

## Czy taki kod wyświetli liczbę 0, jeśli nie dlaczego?

{% highlight javascript %}
var funs = [];
for (var i = 0; i < 10; ++i) {
   funs.push(function() {
      console.log(i);
   });
}

funs[0]();
{% endhighlight %}

Nie zadziała, wyświetli 10, tak jak wywołanie każdej z funkcji ponieważ w języku JavaScript zakres `var` nie jest blokowy (czyli tylko dal bloku for w przykładzie) tylko funkcyjny i każda pętla w forze ma tą samą zmienną dlatego na końcu jak się skończy `for` każda funkcja będzie miała referencje to tej samej zmiennej, która ma wartość 10.

Aby to naprawić wystarczy użyć `let` z ES6:

{% highlight javascript %}
var funs = [];
for (let i = 0; i < 10; ++i) {
   funs.push(function() {
      console.log(i);
   });
}

funs[0]();
{% endhighlight %}


albo stworzyć funkcje ze zmienną `i`, czyli IIFE, który utworzy nowy scope (ang. zasięg) dla tej zmiennej, to rozwiązanie będzie bardziej przenośne między przeglądarkami:

{% highlight javascript %}
for (var i = 0; i < 10; ++i) {
   (function(i) {
       funs.push(function() {
           console.log(i);
       });
   })(i);
}
{% endhighlight %}


## Czy ten kod zadziała?

{% highlight javascript %}
function Foo(number) {
    this.number = number;
    this.add = function(array) {
        return array.map(function(number) {
            return this.number + number;
        });
    }
}

var a = new Foo(10);
console.log(a.add([1,2,3,4]));
{% endhighlight %}

Nie ponieważ `this` w funkcji `map` będzie to obiekt `window` albo zwróci wyjątek `TypeError` jeśli będzie użyty `"strict mode"`, aby to naprawić można użyć tzw. arrow function z ES6 w map:

{% highlight javascript %}
function Foo(number) {
    this.number = number;
    this.add = function(array) {
        return array.map((number) => {
            return this.number + number;
        });
    }
}
{% endhighlight %}


lub zapisać `this` do zmiennej (bardziej przenośne rozwiązanie) np.:

{% highlight javascript %}
function Foo(number) {
    this.number = number;
    this.add = function(array) {
        var self = this;
        return array.map(function(number) => {
            return self.number + number;
        });
    }
}
{% endhighlight %}

## Co wyświetli poniższy kod?


{% highlight javascript %}
var array = [2,3,4,5];
for (var i in array) {
    console.log(i);
}
{% endhighlight %}

Wyświetli:

```
0
1
2
3
```

`for..in` operuje na kluczach, w przypadku tablic są to indeksy aby iterować po wartościach tablicy można użyć normalnego fora:

{% highlight javascript %}
var array = [2,3,4,5];
for (var i=0; i<array.length; ++i) {
    console.log(array[i]);
}
{% endhighlight %}

*[ES6]: ECMAScript 6
*[IIFE]: Immediately-Invoked Function Expression
