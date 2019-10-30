---
layout: post
title:  "Co to jest Transducer?"
date:   2019-02-16 00:07:10+0100
categories:
tags: javascript programowanie-funkcyjne
author: jcubic
description: Transducer wcale nie jest taki straszny, w tym wpisie przedstawię co to jest i jak go utworzyć w języku JavaScript.
image:
 url: "/img/magic_cards.jpg"
 alt: "Kobieta w Kapeluszu rzucająca karty w powietrze"
 width: 800
 height: 465
 attribution: Źródło [pxhere.com](https://pxhere.com/en/photo/633636) Licencja [CC0 Public Domain](https://creativecommons.org/publicdomain/zero/1.0/)
---

Dzisiaj przedstawię ciekawą koncepcje z programowania funkcyjnego, a mianowicie transducer, czyli
według Wikipedii przetwornik.

<!-- more -->

## Wstęp

Transducer'y są częścią programowania funkcyjnego. Dzięki nim możemy zawierać skomplikowane
operacje, które normalnie wymagają takich funkcji jak map czy filter, które ze względu na różne
sygnatury (do funkcji przekazujemy inne argumenty i co innego zwracamy), nie można składać
(ang. compose). Wygląda to tak, że zapisujemy operacje map czy filter pod postacią reducer'a, czyli
funkcji, którą można użyć w metodzie reduce dla tablic, lub jej odpowiednika dla innych
obiektów. Jeśli mamy kilka reducer'ów, które mają być uruchomione jeden za drugim, możemy je złożyć
ze sobą do postaci pojedynczego reducer'a, który możemy wtedy przekazać do metody reduce. Dzięki
temu przetwarzamy tablicę/listę lub inną strukturę tylko raz, co przyspiesza działanie programu.

Jest to możliwe, ponieważ funkcja reduce jest bardzo potężna, nazywana czasami scyzorykiem
szwajcarskim operacji na tablicach.

Przyjrzyj się poniższemu kodowi:

{% highlight javascript %}
array
  .map(fn1)
  .filter(fn2)
  .reduce(fn3);
{% endhighlight %}

W tej funkcji operacja iteracji po elementach nastąpi 3 razy, pomyśl co się stanie, gdy tablica
będzie bardzo duża, albo gdy mamy do czynienia ze strumieniem. Taka operacja jest bardzo
kosztowna. Na szczęście, dzięki ludziom sprytniejszym ode mnie, mamy możliwość wykonania składania
(ang. compose) tych funkcji, ale najpierw musimy je przetworzyć, aby uzyskać funkcje postaci takiej
jak funkcja sum, którą można przekazać do funkcje `reduce`.

{% highlight javascript %}
var sum = (accumulator, element) => accumulator + element;
{% endhighlight %}

Uwaga: Wszędzie w kodzie używam var zamiast const aby nie utrudniać życia gdy używa się konsoli i
chce się jeszcze raz zdefiniować daną funkcje. W docelowym kodzie można użyć const dla definiowanych
funkcji.

## Kompozycja funkcji

Na początek w skrócie co to jest składanie. Może pamiętasz z matematyki w szkole średniej, jest to
łączenie dwóch funkcji w jedną funkcje. Zazwyczaj w matematyce zaznacza się to tak:

```
h(x) = f ∘ g = g(f(x))
```

W JavaScript wygląda to tak:

{% highlight javascript %}
function compose(f, g) {
   return function(x) {
      return g(f(x));
   }
}

var square = (x) => x * x;
var minus1 = (x) => x - 1;
{% endhighlight %}

gdy musimy wywołać obie funkcje wielokrotnie na różnych liczbach, np.:

{% highlight javascript %}
minus1(square(10));
minus1(square(20));
minus1(square(30));
{% endhighlight %}

warto jest utworzyć jedną funkcje, która wykona obie operacje. Możemy to zrobić za pomocą funkcji
`compose`:

{% highlight javascript %}
var square_1 = compose(square, minus1);

square_1(10);
square_1(20);
square_1(30);
{% endhighlight %}

Poniżej przykład bardziej uniwersalnej funkcji `compose`, która działa dla wielu funkcji:

{% highlight javascript %}
var compose = (...fns) => x => fns.reduceRight((y, f) => f(y), x);
{% endhighlight %}

reduceRight to funkcja, która działa jak `reduce`, ale zwija wyniki w odwrotnej kolejności.

> Jeśli nie znasz jeszcze funkcji `map`, `reduce` oraz `filter` dla tablic, koniecznie przeczytaj
> najpierw artykuł
> [Wszystko co powinieneś wiedzieć o funkcjach w JavaScript](/2014/08/funkcje-w-javascript.html).

## Funkcje map oraz filter jako reducer

Wracając do transducer'ów i naszego pierwszego przykładu:

{% highlight javascript %}
array
    .map(fn1)
    .filter(fn2)
    .reduce(fn3);
{% endhighlight %}

Napiszmy te funkcje jako reducer'y, najpierw map:

{% highlight jsnext %}
var map = fn => (accumulator, element) => accumulator.concat(fn(element));
{% endhighlight %}

mając taką funkcje, możemy zamiast `Array::map`, użyć `Array::reduce`, w celu uzyskania takiego
samego efektu jak `Array::map`:

{% highlight javascript %}
[0, 1, 2, 3, 4, 5, 6].reduce(map((x) => x * x), []);
// [0, 1, 4, 9, 16, 25, 36]
{% endhighlight %}

kolejna funkcja to filter jako reducer:

{% highlight javascript %}
const filter = fn => (accumulator, element) =>
    fn(element) ? accumulator.concat(element) : accumulator;

[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].reduce(filter((x) => x % 2 == 0, []);
// [2, 4, 6, 8, 10]
{% endhighlight %}

Mając te dwie funkcje, możemy je połączyć:

{% highlight javascript %}
0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
    .reduce(map(x => x + 1), [])
    .reduce(filter(x => x % 2 === 0), []);
    // [2, 4, 6, 8, 10]
{% endhighlight %}

W obu reducer'ach mamy operacje, która łączy element z akumulatorem czyli wynikową wartością, która
rośnie w miarę wykonywania reducer'a:

{% highlight javascript %}
var map = fn => (accumulator, element) => accumulator.concat(fn(element));

var filter = fn => (accumulator, element) =>
    fn(element) ? accumulator.concat(element) : accumulator;
{% endhighlight %}

Powyższe funkcje wyglądają tak samo jest funkcja sum:

{% highlight javascript %}
const sum = (akumulator, element) => akumulator + element;
{% endhighlight %}

w tym przypadku `sum`, operatorem jest `+` natomiast w naszych funkcjach, jest to `Array::concat`.
Możemy wyciągnąć tą operacje na zewnątrz i umieścić w funkcji, którą przekażemy jako argument:

{% highlight javascript %}
// Ważna jest kolejność, najpierw funkcja a potem łącznik
var map = f => reducing => (result, input) =>
    reducing(result, f(input));

var filter =  predicate => reducing => (result, input) =>
    predicate(input) ? reducing(result, input) : result;
{% endhighlight %}

Mając takie funkcje możemy ich użyć w taki sposób:

{% highlight javascript %}
var concat = (xs, x) => xs.concat(x);

[0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
    .reduce(map(x => x + 1)(concat), [])
    .reduce(filter(x => x % 2 === 0)(concat), []);
    // [2, 4, 6, 8, 10]
{% endhighlight %}

## Łączenie reducer'ów

Zauważ że sygnatura funkcji `concat`, która występuje jako parametr `reducing`, czyli nasz łącznik
wynikowy:

{% highlight javascript %}
var concat = (xs, x) => xs.concat(x);
{% endhighlight %}

ma postać

{% highlight javascript %}
(result, element) => result
{% endhighlight %}

tak samo jak reducer (funkcja przekazywane do `reduce`) tak samo jak i w funkcji
map czy funkcji filter. Dlatego też można zastąpić funkcję `concat` innym reducer'em np.:

{% highlight javascript %}
var reducer = filter(x => x % 2 === 0)(concat);
map(x => x + 1)(reducer);
{% endhighlight %}

Można to zapisać w jednej linijce. Sygnatura takiej funkcji jest dokładnie taka sama jak:

{% highlight javascript %}
map(x => x + 1)(concat)
{% endhighlight %}

można więc ją użyć w funkcji `reduce`:

{% highlight javascript %}
[0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
    .reduce(map(x => x + 1)(filter(x => x % 2 === 0)(concat)), [])
// [2, 4, 6, 8, 10]
{% endhighlight %}

teraz już jest fajniej, ponieważ mamy tylko jedną iteracje po tablicy.

## Transducer

Możemy uprościć ten kod używając funkcji `compose`:

{% highlight javascript %}
var transducer = compose(map(x => x + 1), filter(x => x % 2 === 0));

[0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
    .reduce(transducer(concat), []);
// [2, 4, 6, 8, 10]
{% endhighlight %}

**Więc czym dokładnie jest transducer?** Jest to funkcja, która opisuje jakiś algorytm, utworzona za
pomocą pojedynczej funkcji lub za pomocą ich składania (ang. function composition). Transducer jest
uniwersalny i abstrakcyjny ponieważ dopiero funkcja z jaką go wywołamy określa co transducer
przetwarza. W tym przykładnie transducer został wywołany z funkcją `concat` i operuje na tablicach
ale nic nie szkodzi na przykładnie aby transducer operował np. na obietnicach. Oto przykład:

{% highlight javascript %}
var promise_resolver => (acc, promise) => {
    return acc.then(x => promise.then(y => x + y));
}

var filter =  fn => reducing => async (result, input) =>
    await fn(input) ? reducing(result, input) : result;

var transducer = compose(
    map(async (x) => (await x) + 1),
    filter(async (x) => (await x) % 2 === 0)
);

// jeśli korzystasz z konsoli w Google Chrome możesz pominąć wrapper async.
(async function() {
    // przygotowanie tablicy obietnic
    var promises = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9].map(n => Promise.resolve(n));

    var result = await promises.reduce(transducer(promise_resolver), Promise.resolve(0));
    console.log(result); // 30
})();
{% endhighlight %}

Z powyższego kodu uzyskamy wynik 30 czyli tyle samo co:

{% highlight javascript %}
var transducer = compose(mapping(x => x + 1), filtering(x => x % 2 === 0));

[0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
    .reduce(transducer((a,b) => a + b), 0);
    // 30
{% endhighlight %}

niestety funkcja `filter` wymagała przepisania na funkcje asynchroniczną, ponieważ predykat zwraca
Obietnicę. Nie musimy natomiast nic więcej robić z funkcją `map` oraz wywołaniem funkcji `reducing`
w funkcji `filter`, ponieważ kontroluje go nasz łącznik (funkcja `concat`).

Można także poprawić obie funkcje oraz łącznik, aby operowały na obietnicach i aby działały tak samo
na liczbach jak i na obietnicach. Oto jakby wykładał taki kod:

{% highlight javascript %}
var map = fn => reducing => async (result, input) =>
    await reducing(result, fn(await input));

var filter =  fn => reducing => async (result, input) =>
    await fn(await input) ? await reducing(result, input) : result;

var concat = async (xs, x) => (await xs).concat(await x);
{% endhighlight %}

Mając powyższy kod, można użyć naszego pierwszego złączonego transducer'a:

{% highlight javascript %}
var transducer = compose(map(x => x + 1), filter(x => x % 2 === 0));

(async function() {
    var result = await [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
        .reduce(transducer(concat), []);
    console.log(result); // [2, 4, 6, 8, 10]
})();
{% endhighlight %}

tak samo będzie wyglądał kod dla obietnic

{% highlight javascript %}
(async function() {
    var promises = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9].map(n => Promise.resolve(n));

    var result = await promises.reduce(transducer(concat), Promise.resolve([]));
    console.log(result); // [2, 4, 6, 8, 10]
})();
{% endhighlight %}

Niestety dla zwykłych liczb wynikiem także będzie obietnica.

> Jeśli nie znasz jeszcze składni
> `async..await`, albo nie znasz jeszcze obietnic możesz przeczytać o nich w
> [artykułach o asynchronicznym kodzie w JavaScript](/tag/asynchroniczność/).

Poniżej przedstawiam trochę bardziej skomplikowany kod, który sprawdza typ i nie zwraca obietnicy,
gdy wartością nie są obietnice:

{% highlight javascript %}
// funkcja pmocnicza wyższego rzędu, wywołująca funkcje fn z aktualną wartością
var resolve = (value, fn) => {
    if (value instanceof Promise) {
         return value.then(fn);
    } else {
        return fn(value);
    }
};

var map = fn => reducing => (result, input) => {
    return reducing(result, resolve(input, fn));
};

var filter =  predicate => reducing => (result, input) => {
    const ret = cond => cond ? reducing(result, input) : result;
    return resolve(resolve(input, predicate), ret);
}

// connector to też funkcja wyższego rzędu,
// która upraszcza pisanie asynchronicznych funkcji łącznikowych

var connector = fn => (accumulator, element) => {
   return resolve(accumulator, (acc) => {
      return resolve(element, (e) => fn(acc, e));
   });
}

var concat = connector((acc, item) => acc.concat(item));
var sum = connector((a, b) => a + b);

// standardowy transducer
var transducer = compose(map(x => x + 1), filter(x => x % 2 === 0));

// kod asynchroniczny
(async function() {
   var promises = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9].map(n => Promise.resolve(n));

   var result = await promises.reduce(transducer(concat), Promise.resolve([]));
   console.log(result); // [2, 4, 6, 8, 10]
})();

// kod dla zwykłej tablicy
var result = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
    .reduce(transducer(concat), []);
console.log(result); // [2, 4, 6, 8, 10]
{% endhighlight %}

Na koniec [demo na CodePen](https://codepen.io/jcubic/pen/PVxPqz?editors=0011), na którym możesz
poeksperymentować.
