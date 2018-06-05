---
layout: post
title:  "Generatory i Iteratory"
date:   2018-06-01 11:51:09+0200
categories:
tags: javascript es6
author: jcubic
image:
  url: "/img/electronics.jpg"
  alt: "Wnętrze urządzenia elektronicznego"
---
Pętla `for..of` to nowy rodzaj pętli. Pętla ta działa tak jak zwykła pętla `for..in` przy czym elementy w zmiennej
to nie indeksy jak w przypadku `for..in` tylko wartości. Razem z tą pętlą wprowadzono do języka nowy protokół czyli
iteratory oraz cukier syntaktyczny czyli generatory.

<!-- more -->

{% highlight javascript %}
for (const i in [2,3,4,5]) {
   console.log(i);
}
{% endhighlight %}

powyższy kod wyświetli liczby od 0 do 3. Natomiast:

{% highlight javascript %}
for (const i of [2,3,4,5]) {
   console.log(i);
}
{% endhighlight %}

Razem z pętlą `for..of` do języka JavaScript weszło nowe API, które umożliwia napisanie własnego obiektu, który
jest iteratorem. To API nazywane protokołem, wygląda tak, że musimy utworzyć nowy obiekt, który będzie miał iterator
pod kluczem `Symbol.iterator`.

Iterator to funkcja, która zwraca obiekt z metodą `next`, która z kolei zwraca obiekty:

{% highlight javascript %}
{value: <WARTOŚĆ>, done: boolean}
{% endhighlight %}

Wartość `done` określa czy iteracja się skończyła, natomiast `value` to wartość, która zostanie przypisana
do zmiennej.

Przykład kodu:

{% highlight javascript %}
function numbers(n) {
    return {
        [Symbol.iterator]: function() {
            var i = 0;
            return {
                next: function() {
                    if (i <= n) {
                        // wartość done jest opcjonalna ponieważ undefined działa jak false
                        return {value: i++};
                    }
                    return {done: true}; // tak jak tutaj value
                }
            }
         }
   };
}

for (const n of numbers(10)) {
    console.log(n);
}
{% endhighlight %}

Powyższy kod wyświetli liczby od 0 do 10. Iteratory to niskopoziomowe API, aby uprościć ich użycie dodano jeszcze
generatory. Aby utworzyć generator tworzymy nową funkcje z gwiazdką i używamy słowa kluczowego `yield`, aby zwracać
elementy. Generator będzie się zatrzymywał za operatorem `yield` i wznawiał swoje działanie za nim. Oto przykład:

{% highlight javascript %}
function random_range(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function* random_numbers(n) {
    while(--n >= 0) {
       yield random_range(0, 100);
    }
}

for (const n of random_numbers(10)) {
    console.log(n);
}
{% endhighlight %}

Warto wspomnieć jeszcze o operatorze yield z gwiazdką, czyli `yield*` służy on do "odpakowywania" innego generatora
wewnątrz generatora.

{% highlight javascript %}
function random_range(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
function* random_numbers(n, max) {
    while(--n >= 0) {
       yield random_range(0, max);
    }
}

function* parts(n) {
   while (--n >= 3) {
      yield* random_numbers(3, n);
   }
}

for (const n of parts(10)) {
    console.log(n);
}
{% endhighlight %}

Funkcja wyświetli 21 losowych liczb `(10 - 3) * 3` z coraz mniejszym zakresem.

Wartość zwracana przez generator jest dokładnie taka sama jak nasz pierwszy iterator.

{% highlight javascript %}
var obj = random_numbers(3);

typeof obj[Symbol.iterator];
// function
var iter = obj[Symbol.iterator]();
iter.next(); // {value: 98, done: false}
iter.next(); // {value: 56, done: false}
iter.next(); // {value: 10, done: false}
iter.next(); // {value: undefined, done: true}
{% endhighlight %}

Protokół iteratorów jest zaimplementowane przez wbudowane typy takie jak tablice czy ciągi znaków.
Czyli można ich używać z pętlą for..of.

{% highlight javascript %}
typeof ""[Symbol.iterator]; // "function"
typeof [][Symbol.iterator]; // "function"
{% endhighlight %}

Aby zobaczyć wsparcie dla generatorów oraz iteratorów możesz zajrzeć na stronę
[can I use](https://caniuse.com/#feat=es6-generators), zaimplementowane są w większości przeglądarek,
oprócz IE oraz Opera Mini.

Jeśli chcesz się pobawić generatorami i iteratorami, [tutaj masz demo na codpen](https://codepen.io/jcubic/pen/gzoQRo).

Ciekawostką może być że generatory są używane, przez narzędzie Babel, do zaimplementowania async/await, o którym pisałem w
[drugim wpisie o asynchroniczności](/2018/05/asynchronicznosc-async-await.html).
