---
layout: post
title:  "5 pytań na rozmowę rekrutacyjną z języka JavaScript"
date:   2017-09-08 22:56:53+0200
categories:
tags:  javascript praca
author: jcubic
description: Oto 5 pytań jakie bym zadał na rozmowie rekrutacyjnej z języka JavaScript, czyli najważniejsza część Front-Endu. Takie pytania mogą pojawić się na rozmowie kwalifikacyjnej z JS.
related:
  -
    name: "15 Pytań na rozmowę rekrutacyjną z CSS"
    url: "/2018/10/pytania-rekrutacyjne-css.html"
  -
    name: "Kolejne 10 pytań na rozmowę rekrutacyjną z języka JavaScript"
    url: "/2019/03/kolejne-pytania-na-rozmowe-rekrutacyjna-javascript.html"
  -
    name: "15 Pytań na rozmowę rekrutacyjną z HTML5"
    url: "/2019/11/pytania-rekrutacyjne-html5.html"

sitemap:
  lastmod: 2019-11-30 13:17:22+0100
---

Kilka dni temu zostałem poproszony o przygotowanie 3 pytań, dla kandydata na programistę Full Stack, do zbliżającej
się rozmowy kwalifikacyjnej, z języka JavaScript. Przygotowałem 4 i potem dodałem jeszcze jedno. Oto one.

<!-- more -->

## Co to są domknięcia (ang. closures)?

W skrócie, domknięcia są to funkcje, które mają dostęp do środowiska, w którym zostały zdefiniowane.
Pytanie z domknięć to chyba najpopularniejsze pytanie na rozmowach rekrutacyjnych.

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

Wywołanie funkcji `c()` zwróci liczbę 10, mimo że zakres dla zmiennej b się skończył, kiedy skończyła się funkcja a.
Zmienna b jest nadal jednak dostępna wewnątrz funkcji wewnętrznej, dzięki czemuś o nazwie domknięcia.


Mówiąc dokładnie domknięcie to funkcja, która ma dostęp do środowiska, w którym została zdefiniowana.
Co najlepiej jest widoczne, gdy zwracamy funkcje (czyli w funkcjach wyższego rzędu).

## Co to jest hoisting?

Deklaracje funkcji i zmiennych są przenoszone na początek funkcji, w której zostały zdefiniowane,
ale już przypisanie nie np:

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


Dokładny mechanizm wygląda tak, że interpreter "przechodzi" kod dwa razy, za pierwszym "wyciągając" wszystkie deklaracje.
Dlatego przy drugim przejściu są na początku bloku, ponieważ są już stworzone wewnętrzne referencje. Tak naprawdę
hoisting, nie jest to coś, co znajduje się w specyfikacji ECMAScript (nigdzie nie występuje słowo hoisting),
oraz zaimplementowane w silnikach JavaScript, ale wyjaśnienie działania, któremu nadano taką właśnie nazwę.

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

Nie zadziała, wyświetli 10, tak jak wywołanie każdej z funkcji, ponieważ w języku JavaScript zakres `var` nie jest blokowy (czyli tylko dla bloku for w przykładzie) tylko funkcyjny. Każda pętla w forze ma tą samą zmienną. Dlatego na końcu jak się skończy `for`, każda funkcja będzie miała referencje to tej samej zmiennej, która ma wartość 10.

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

Nie ponieważ `this` w funkcji `map` będzie to obiekt `window` albo zwróci wyjątek `TypeError` jeśli będzie użyty `"strict mode"`, aby to naprawić można użyć tzw. funkcji strzałkowej (ang. arrow function) z ES6 w map:

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

`for..in` operuje na kluczach. W przypadku tablic są to indeksy, aby iterować po wartościach tablicy, można użyć normalnego fora:

{% highlight javascript %}
var array = [2,3,4,5];
for (var i=0; i<array.length; ++i) {
    console.log(array[i]);
}
{% endhighlight %}

Albo użyć pętli z ES6 `for..of`:

{% highlight javascript %}
var array = [2,3,4,5];
for (const i of array) {
    console.log(i);
}
{% endhighlight %}

można także użyć funkcji `forEach` z ES5.


*[ES6]: ECMAScript 6
*[ES5]: ECMAScript 5
*[IIFE]: Immediately-Invoked Function Expression
