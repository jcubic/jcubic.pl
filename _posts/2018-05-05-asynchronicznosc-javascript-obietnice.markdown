---
layout: post
title:  "Asynchroniczność w JavaScript cz. 1: Obietnice"
date:   2018-05-05 17:02:13+0200
categories:
tags:  javascript es6 asynchroniczność
author: jcubic
description: Obiekty typu Promise czyli obietnice, są sposobem na zwięzłą implementacje asynchronicznego kodu. Będzie to pierwszy wpis z serii omawiającej asynchroniczność w JavaScript.
image:
  url: "/img/letter.jpg"
  alt: "List w kopercie oraz długopis"
sitemap:
  lastmod: 2019-01-07 12:28:06+0100
related:
  -
    name: "Asynchroniczność cz. 2: Async/Await"
    url: /2018/05/asynchronicznosc-javascript-async-await.html
  -
    name: "Asynchroniczność cz. 3: Iteratory i Generatory Asynchroniczne"
    url: /2018/06/asynchronicznosc-javascript-for-await-of.html
  -
    name: "Asynchroniczność cz. 4: Funkcja async jako generator"
    url: /2018/06/asynchronicznosc-javascript-funkcja-async-jako-generator.html
---

Będzie to pierwszy wpis z serii postów o asynchronicznym kodzie w języku JavaScript. Na początek obiekty typu `Promise`,
czyli obietnice jakieś wartości.

<!-- more -->

Na początku kiedy została "odkryta" możliwość używania obiektów `XMLHttpRequest`, do wysyłania zapytań typu HTTP do
serwera (czyli AJAX), zaczęto używać funkcji zwrotnych (ang. callback) aby wywoływać kod jak już dane będą
dostępne. Takie same funkcje używa się też w funkcji `setTimeout`. W miarę pisania coraz bardziej skomplikowanych
aplikacji można było wpaść w pułapkę tzw. piekła funkcji zwrotnych (ang. callback hell). Przykład takiego kodu:


{% highlight javascript %}
getUsers(function(users) {
    users.forEach(function(user) {
        getProducts(user.id, function(products) {
            var total = 0;
            products.forEach(function(product, i) {
                getPrice(product.id, function(price) {
                    product.price = price;
                    getQuantity(user.id, product.id, function(quantity) {
                        total += product.price * quantity;
                        if (products.length - 1 == i) {
                            console.log(user.name + ' ' + total);
                        }
                    });
                });
            });
        });
    });
});
{% endhighlight %}


W powyższym kodzie użyto tylko pięciu funkcji zwrotnych, ale przy bardziej skomplikowanym kodzie może być ich więcej.


Rozwiązaniem tego problemu były obiekty typu `Promise`, czyli obietnice, które zawierają wartość, która zostanie
dostarczona w przyszłości (można także utworzyć ten obiekt z natychmiastową wartością, ona także będzie
asynchroniczna). Obiekty te są częścią ECMAScript w wersji 6.


Pierwszy raz spotkałem się z obietnicami, poprzez jeden z pierwszych frameworków JavaScript, czyli
[Dojo toolkit](https://pl.wikipedia.org/wiki/Dojo_Toolkit)
([ostatnio wydano nową wersje 2.0 pod nazwą dojo](https://dojo.io/blog/2018/05/02/2018-05-02-Dojo2-0-0-release/)),
który posiadał funkcje, konstruktor Deferred. Był to chyba pierwszy framework, który dodał ten obiekt (jeśli znasz
inną bibliotekę/framework, która posiadała obiekty defered przed dojo, nawet w innym języku, to pisz w komentarzu).
Było to lata przed tym jak powstały jQuery i Angular. Napisałem nawet, dawno temu,
[tutorial na temat dojo toolkit](https://jcubic.pl/jakub-jankiewicz/dojo_tutorial.php).


Aby utworzyć obiekt typu `Promise`, trzeba użyć konstruktora, o takiej właśnie nazwie, do którego przekazujemy funkcję z dwoma
argumentami, które także są funkcjami. Po wywołaniu, spełnią obietnice lub ją odrzucają.


{% highlight javascript %}
var hello = new Promise(function(resolve, reject) {
    setTimeout(function() {
        if (success) {
            resolve('hello');
        } else {
            reject('error');
        }
    }, 1000);
});
{% endhighlight %}


Aby otrzymać wartość z obiektu obietnicy, należy użyć funkcji `then`, przekazując do niej funkcje, której argumentem będzie
wartość obietnicy.


{% highlight javascript %}
hello.then(function(string) {
    console.log(string);
}).catch(function(e) {
    console.log('error ' + e);
});
{% endhighlight %}


Obietnice nie pozbywają się funkcji zwrotnych, ale za ich pomocą można "spłaszczyć" zagnieżdżone funkcje. Poniżej pierwszy
przykład zapisany za pomocą obietnic.


{% highlight javascript %}
getUsers().then(function(users) {
    return Promise.all(users.map(function(user) {
        return Promise.all([user, getProducts(user.id)]);
    }));
}).then(function(data) {
    return Promise.all(data.map(function([user, products]) {
        var total_promise = products.reduce(function(promise, product) {
            return promise.then(function(total) {
                return Promise.all([getPrice(product.id),
                                    getQuantity(user.id, product.id)])
                    .then(([price, count]) => total + (count * price));
            });
        }, Promise.resolve(0));
        return Promise.all([user, total_promise]);
    }));
}).then(function(data) {
    data.forEach(function([user, total]) {
        console.log(user.name + ' ' + total);
    });
}).catch(function() {
    console.log('Błąd w którejś obietnicy, nigdy się nie wywoła');
});
{% endhighlight %}


Kod trochę się skomplikował, ponieważ mieliśmy dwie tablice z użytkownikami oraz produktami, przykład z funkcjami
zwrotnymi był o wiele czytelniejszy, ale to tylko przykład. Pierwsza funkcja then jest jednak dość czytelna, a w
następnej mamy już dostęp do tablicy dwuelementowych tablic, gdzie pierwszy element to użytkownik a drugi do tablica
produktów.


Fajną funkcją obietnic jest to, że możemy użyć funkcji `reduce`, aby "zwinąć" listę produktów do postaci
pojedynczej liczby i nie potrzebujemy już sprawdzać, czy funkcja jest ostatnią w tablicy.


Aby uzyskać równoległe wywołanie wszystkich funkcji `getPrice` i `getQuantity` można wywołać `map` a następnie `reduce`.
Zapisanie równoległe pierwszego przykładu, z użyciem funkcji zwrotnych, dodatkowo pogmatwałoby ten kod.


{% highlight javascript %}
var total_promise = products.map((product) => [
    getPrice(product.id),
    getQuantity(user.id, product.id)
]).reduce(function(promise, arr) {
    return promise.then(function(total) {
        return Promise.all(arr).then(([price, count]) => total + (count * price));
    });
}, Promise.resolve(0));
{% endhighlight %}


Inną fajną cechą obietnic jest dodawanie obsługi błędów. Jeśli chcielibyśmy dodać ich obsługę do pierwszego
przykładu, nasz kod jeszcze bardziej by się skomplikował, ponieważ musielibyśmy dodawać do każdej
funkcji drugą, która wywołałaby się w przypadku błędu lub tak jak w przypadku node.js przekazywać błąd jako pierwszy
argument lub null w przypadku jego braku. Aby funkcja `catch` się wywołała, wystarczy w dowolnej funkcji wywołać
`reject` lub wyrzucić wyjątek. Dzięki temu będziemy mieli tylko jedno miejsce obsługi błędów. Nie musimy też dodawać
try..catch ponieważ wszystkie wyjątki, nawet te z silnika JavaScript, jak `type` albo `range` `error`, także trafią do
funkcji catch.


Obiekty typu `Promise` (obietnice) zostały zaimplementowane w większości przeglądarek (oprócz IE). Aby zobaczyć,
które wersje przeglądarek zaimplementowały to API możesz zerknąć na
[stronę MDN](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise) lub
na [tabelę poszczególnych funkcjonalności es6](https://kangax.github.io/compat-table/es6/).


Inna nowość w ECMAScript to funkcja fetch, której API bazuje na obietnicach. Zastępuje ona obiekt XMLHttpRequest i udostępnia
prostsze API.


{% highlight javascript %}
fetch(`/users/${id}`).then(function(response) {
    return response.json();
}).then(function(user) {
    console.log(user.name);
}).catch(function() {
    console.log('parse error');
});
{% endhighlight %}

Funkcja [fetch jest zaimplementowana w prawie wszystkich przeglądarkach](https://caniuse.com/#feat=fetch) oprócz IE
oraz Opera mini. Aby użyć kodu, który korzysta z funkcji `fetch` w przeglądarkach, które nie zaimplementowały tego API,
można skorzystać [implementacji zastępczej (ang. polyfill) napisanej przez firmę GitHub](https://github.com/github/fetch).

Fetch można także używać w node.js, poprzez dwa pakiety npm: [node-fetch](https://www.npmjs.com/package/node-fetch) lub
[whatwg-fetch](https://www.npmjs.com/package/whatwg-fetch).
