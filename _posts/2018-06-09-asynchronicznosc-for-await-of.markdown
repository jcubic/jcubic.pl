---
layout: post
title:  "Asynchroniczność cz. 3: Iteratory i Generatory Asynchroniczne"
date:   2018-06-09 10:12:31+0200
categories:
description: Wpis opisjuący nową propozycje ECMAScript ES2018, w której można używać asynchronicznych generatorów i iteratorów tak jak zwykłych ich odpowiedników za pomocą pętli for..await..of
tags: javascript es2018 asynchroniczność
author: jcubic

image:
  url: "/img/old-pocket-watch.jpg"
  alt: "Stary zegarek kieszonkowy"
related:
  -
    name: "Asynchroniczność cz. 1: Obietnice"
    url: /2018/05/asynchronicznosc-obietnice.html
  -
    name: "Asynchroniczność cz. 2: Async/Await"
    url: /2018/05/asynchronicznosc-async-await.html
  -
    name: "Iteratory i Generatory"
    url: /2018/06/iteratory-i-generatory.html
---

Aby iterować po tablicach, do tej pory w języku JavaScript były dostępne dwa rodzaje pętli `for`,
obie iterowały po indeksach. Nowością jest nowy operator `of` oraz jego użycie w pętli `for`.

W tym wpisie opiszę nową propozycje ECMAScript, która wejdzie do standardu ES2018, której już można używać w
przeglądarkach oraz node (wersje przed v10 wymagają opcji `--harmony-async-iteration`), jaką jest asynchroniczna
pętla `for..of` za pomocą słowa kluczowego `await`.

<!-- more -->

Istnieją jej dwa rodzaje (`await` można wstawić w dwóch miejscach):

{% highlight javascript %}
(async () => {
    for (const item of await iterator) {
        console.log(item);
    }
})()
{% endhighlight %}

W tym przypadku iterator to po prostu obietnica, na którą trzeba zaczekać. Jest to odpowiednik:

{% highlight javascript %}
(async () => {
    const value = await iterator;

    for (const item of value) {
        console.log(item);
    }
})();
{% endhighlight %}


Drugie użycie to:

{% highlight javascript %}
(async () => {
    for await (const item of iterator) {
        console.log(item);
    }
})();
{% endhighlight %}

Ten przepadek jest już bardziej ciekawy. Aby można było użyć `await` w tym miejscu iterator musi zwracać obietnice
wartości, czyli iterujemy po obietnicach. Pętla będzie się zatrzymywać po każdej iteracji dopóki poprzednia wartość
się nie spełni.

Jeśli jesteś zaznajomiony z generatorami i tym że generatory to po prostu cukier syntaktyczny dla iteratorów (jeśli
nie to polecam przeczytać wpis o [generatorach i iteratorach](/2018/06/iteratory-i-generatory.html)), to szczegóły
iteratorów oraz generatorów asynchronicznych nie będą trudne. Iterator asynchroniczny jest podobny do zwykłego,
przy czym kluczem dla iteratora jest `Symbol.asyncIterator` (dla normalnego iteratora jest to `Symbol.iterator`)
oraz funkcja `next` musi zwracać obietnice. Funkcja ta może też być funkcją `async`, w której można używać słowa
kluczowego `await`, ale nie jest to obowiązkowe.


Najpierw przyjrzyjmy się wnętrznościom czyli iteratorom. Oto przykład zwykłego asynchronicznego iteratora:

{% highlight javascript %}
function requests(urls) {
    urls = urls.slice(); // tworzymy kopie aby nie modyfikować oryginalnej tablicy
    return {
        [Symbol.asyncIterator]: function() {
            return {
                next: function() {
                    var url = urls.shift();
                    if (url) {
                        // done jest zbędne, ponieważ w JavaScript undefined jest wartością
                        // typu false
                        return fetch(url).then(res => res.text()).then(text => ({value:text}));
                    }
                    return {done: true}; // tak jak tutaj value
                }
            }
         }
   };
}
{% endhighlight %}

ważne jest aby nie zwracać `{value: promise}` tylko `promise.then(value => ({value}))`.

Tego iteratora można użyć w ten sposób:

{% highlight javascript %}
(async () => {
    var urls = ['https://jcubic.pl', 'https://terminal.jcubic.pl', 'https://jcubic.github.io/git/'];
    for await (const text of requests(urls)) {
        console.log(text.match(/<title>([^<]+)<\/title>/)[1]);
    }
})();
{% endhighlight %}

Teraz przykład iteratora, który używa `async`. To słowo kluczowe musi być dodane do funkcji `next`, ponieważ to ona
jest wywoływana przy każdej iteracji aby zwracać wartość.

{% highlight javascript %}
function delay(n) {
    return new Promise((resolve) => setTimeout(resolve, n));
}

function delayedNumbers(n) {
    return {
        [Symbol.asyncIterator]: function() {
            var i = 0;
            return {
                next: async function() {
                    if (i++ < n) {
                         await delay(1000);
                         return {value: i};
                    }
                    return {done: true};
                }
            }
         }
   };
}

(async () => {
  for await (let n of delayedNumbers(5)) {
    console.log(n);
  }
})();
{% endhighlight %}

Teraz najlepsze, ponieważ najkrótsze, czyli generatory asynchroniczne:

{% highlight javascript %}
// funkcja pomocnicza ze stack overflow
function rand(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

async function* randomNumbers(min, max) {
  while (true) {
    await delay(1000);
    yield rand(min, max);
  }
}
{% endhighlight %}

tego generatora możemy użyć w ten sposób:

{% highlight javascript %}
(async () => {
  for await (let n of randomNumbers(1, 20)) {
    console.log(n);
  }
})();
{% endhighlight %}

Powyższy kod będzie w nieskończoność produkował losowe liczby, dopóki nie ubijemy procesu lub strony, na której
wywoływany jest ten kod.

Tak jak w przypadku zwykłych generatorów, można także używać operatora `yield*`, aby "odwijać" inne generatory
wewnątrz generatora.

Należy pamiętać że generatory to tylko cukier syntaktyczny nad iteratorami (dokładnie generator to funkcja, która
zwraca iterator). Dlatego popatrz na poniższy kod, w którym korzysta się z generatora tak jak z iteratora, ponieważ
udostępnia on to samo API.

{% highlight javascript %}
async function* titles(urls) {
   for (const url of urls) {
      const res = await fetch(url);
      const text = await res.text();
      try {
          yield text.match(/<title>([^<]+)<\/title>/)[1];
      } catch(e) {
          yield null;
      }
   }
}

var urls = ['https://jcubic.pl', 'https://terminal.jcubic.pl', 'https://jcubic.github.io/git/'];
const iter = titles(urls)[Symbol.asyncIterator]();

iter.next().then(x => console.log(x));
    // { value: 'Głównie JavaScript'}
iter.next().then(x => console.log(x));
    // { value: 'jQuery Terminal Emulator Plugin' }
iter.next().then(x => console.log(x));
    // { value: 'GIT Web Terminal' }
iter.next().then(x => console.log(x));
    // { done: true }
{% endhighlight %}

Generatory i iteratory asynchroniczne nie są tak straszne jakby się wydawało. W kodzie warto używać generatorów, ale
warto także znać wewnętrzną zasadę ich działania, czyli iteratory. Lista przeglądarek, które wspierają `for..await..of`
dostępna jest na stronie [kangax.github.io/compat-table](http://kangax.github.io/compat-table/es2016plus/).
Z chwilą pisania tego artykuły zaimplementowały je Chrome, Firefox oraz Safari.
