---
layout: post
title:  "Generatory i Iteratory wyższego poziomu"
date:   2018-07-10 10:11:30+0200
categories:
tags: javascript es6 es8 es2018
author: jcubic
image:
  url: "/img/microprocessor.jpg"
  alt: "Microprocesor"
  attribution: Jakub Jankiewicz, żródło [Flickr](https://www.flickr.com/photos/jcubic/12237986156), licencja [CC BY-SA](https://creativecommons.org/licenses/by-sa/2.0/)
related:
  -
    name: "Iteratory i Generatory"
    url: /2018/06/iteratory-i-generatory.html
  -
    name: "Asynchroniczność cz. 3: Iteratory i Generatory Asynchroniczne"
    url: /2018/06/asynchronicznosc-for-await-of.htm
---

Ten wpis został zainspirowany
[filmikiem na YouTube na kanale FunFunFunction](https://www.youtube.com/watch?v=lGg43tcQ5x4).
@mpj omawia tylko iterator map, ja poszedłem o krok dalej i opisałem trójce funkcjonalnego
programowania w JS czyli `map`, `reduce` oraz `filter`.

<!-- more -->

* Iterator map

Implementacja iteratora `map`, a właściwie generatora, wygląda podobnie jak normalnej
funkcji `map`, przy czym zamiast tworzyć nową tablicę i dodając element, który zwróci
funkcja, do nowej tablicy używamy słowa kluczowego `yield`.

{% highlight javascript %}
const map = function*(iterable, fn) {
  for (const item of iterable) {
    yield fn(item);
  }
}
{% endhighlight %}

* Iterator filter

{% highlight javascript %}
const filter = function*(iterable, fn) {
  for (const item of iterable) {
    if (fn(item)) {
      yield item;
    }
  }
}
{% endhighlight %}


Poniżej przykład użycia `map` oraz `filter`:

{% highlight javascript %}
// najpierw tworzymy wejściowy iterator range (jak funkcja z Pythona):
function* range(n) {
  let i = 0;
  while(i++ < n) {
    yield i;
  }
}
// funkcje strzałkowe
const even = n => n % 2 == 0;
const square = n => n * 2;
const squares = map(filter(range(10), even), square);
for (const n of squares) {
  console.log(n);
}
{% endhighlight %}

Wynikiem będzie

```
4 8 12 16 20
```

* Iterator reduce

`Reduce` jest trochę bardziej skomplikowany, ponieważ musimy użyć protokołu iteratorów, poniżej funkcja `reduce`:

{% highlight javascript %}
const reduce = function*(iterable, fn, init) {
  const iterator = iterable[Symbol.iterator]();
  let acc = arguments.length == 3 ? init : iterator.next().value;
  while (true) {
    const value = iterator.next();
    if (value.done) {
      break;
    }
    acc = fn(acc, value.value);
    // we yield intermediate values but real reduce should not be generators
    // and it should just return acc at the end
    yield acc;
  }
}
{% endhighlight %}

Tak naprawdę `reduce` to nie powinien być generator tylko funkcja zwracająca pojedynczą wartość `acc` na końcu albo
generator z jednym yield też na końcu, za pętlą `while`. Ale dla testów napisana została jako generator aby można
było obserwować wyniki poszczególnych iteracji:

Poniżej przykład użycia generatora `reduce`, który tworzy silnie od 1 do 10.

{% highlight javascript %}
const inc = n => n+1;
const mul = (a,b) => a*b;
for (const factorial of reduce(map(range(10), inc), mul, 1)) {
  console.log(factorial);
}
{% endhighlight %}

Tutaj [link do demka na CodePen](https://codepen.io/jcubic/pen/OEpowJ?editors=0011) z powyższymi przykładami.

Co ciekawe ponieważ tablice także są iteratorami (implementują protokół iteratorów, więcej informacji na stronie
[Generatory i Iteratory](/2018/06/iteratory-i-generatory.html)) można ich używać z powyższymi funkcjami.

{% highlight javascript %}
for (var i of map([1,2,3,4], (x) => x*2)) {
    console.log(i);
}
// aby uzyskać tablice z iteratora można użyć operatora spread albo Array.from
[...map(range(10), square)];
{% endhighlight %}

Tak samo jak w przypadku zwykłych iteratorów, możemy napisać iteratory `map`, `reduce` oraz `filter` dla iteratorów
asynchronicznych.

* Asynchroniczny iterator map:

{% highlight jsnext %}
const map = async function*(iterator, fn) {
  for await (const item of iterator) {
    yield fn(item);
  }
}
{% endhighlight %}


* Asynchroniczny iterator filter:

{% highlight jsnext %}
const filter = async function*(iterator, fn) {
  for await (const item of iterator) {
    if (fn(item)) {
      yield item;
    }
  }
}
{% endhighlight %}

* Asynchroniczna funkcja reduce

Tym razem poprawna funkcja reduce

{% highlight jsnext %}
const reduce = async function(iterable, fn, init) {
    // aby funkcja była uniwersjalna można sprawdzać oba symbole
    const iterator = iterable[Symbol.asyncIterator]();
    let acc = arguments.length == 3 ? init : iterator.next().value;
    while (true) {
        const value = await iterator.next();
        if (value.done) {
            break;
        }
        acc = fn(acc, value.value);
    }
    return acc;
}
{% endhighlight %}

Przykład użycia

{% highlight jsnext %}
async function* requests(urls) {
    for (const url of urls) {
        yield fetch(url);
    }
}
function terminals(urls) {
    var reqs = requests(urls);
    var texts = map(reqs, res => res.text());
    var titles = map(texts, text => text.match(/<title>([^<]+)<\/title>/)[1]);
    return filter(titles, title => title.match(/terminal/i));
}
function concat_first(acc, title) {
    return (acc ? acc + ' ' : '') + title.split(' ')[0];
}
(async () => {
    var urls = [
        'https://jcubic.pl',
        'https://terminal.jcubic.pl',
        'https://jcubic.github.io/git/'
    ];
    for await (const title of terminals(urls)) {
        console.log(title);
    }
    console.log(await reduce(terminals(urls), concat_first, ''));
})();
{% endhighlight %}

Aby można było skorzystać z iteratora dwa razy, zapisano nasze transformacje wewnątrz funkcji `terminals`.

Na koniec [demko do części asynchronicznej](http://jsbin.com/jemexagezo/edit?js,console),
tym razem na jsBin ponieważ parser JavaScript, który jest na CodePen, nie lubi
asynchronicznych generatorów :(

Ostatnio na [JavaScript Weekly](https://javascriptweekly.com/) (newsletterze dotyczącym
języka JavaScript, który polecam), pojawił się link do biblioteki
[axax](https://github.com/jamiemccrindle/axax), która udostępnia m.i. funkcja omawiane w
tym wpisie, które działają dla obu typów iteratorów.
