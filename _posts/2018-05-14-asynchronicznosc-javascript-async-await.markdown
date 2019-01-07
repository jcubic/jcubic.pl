---
layout: post
title:  "Asynchroniczność w JavaScript cz. 2: Async/Await"
date:   2018-05-14 19:27:26+0200
categories:
tags:  javascript es8 asynchroniczność
author: jcubic
description: Async/Await to dwa nowe słowa kluczowe, dodane do jezyka JavaScript w wersji ES8, które ułatwiają pisanie funkcji które operuja na Obietnicach.
image:
  url: "/img/ducks.jpg"
  alt: "Drewniane, kolorowe, stylizowane kaczki ustawione w kolejce"
sitemap:
  lastmod: 2019-01-07 12:28:06+0100
related:
  -
    name: "Asynchroniczność cz. 1: Obietnice"
    url: /2018/05/asynchronicznosc-javascript--obietnice.html
  -
    name: "Asynchroniczność cz. 3: Iteratory i Generatory Asynchroniczne"
    url: /2018/06/asynchronicznosc-javascript-for-await-of.html
  -
    name: "Asynchroniczność cz. 4: Funkcja async jako generator"
    url: /2018/06/asynchronicznosc-javascript-funkcja-async-jako-generator.html
---

Async/Await to dwa nowe słowa kluczowe dodane do języka JavaScript w wersji
[ES8 (inna nazwa to ES2017)](https://flaviocopes.com/ecmascript/#es2017-aka-es8), które ułatwiają pisanie funkcji, które
operują na obietnicach (ang. promises). Czyli służą do tworzenia funkcji asynchronicznych.

<!-- more -->

Jeśli nie jesteś zaznajomiony z obietnicami, możesz zobaczyć
[pierwszą część o programowaniu asynchronicznym w JavaScript](/2018/05/asynchronicznosc-obietnice.html).

Słowo kluczowe `async`, służy do oznaczania funkcji jako asynchronicznych, które będą operować na obietnicach.
Obietnice natomiast dzięki słowu `await` staja się synchroniczne. Można je przypisywać do zmiennych, wewnątrz których
znajdą się wartości obietnic.

Poniżej przykładowy kod:

{% highlight javascript %}
async function total(username) {
   var res = await fetch("/users/" + username);
   var user = await res.json();
   return user.total;
}
{% endhighlight %}

Użyto dwóch słów kluczowych `await`, ponieważ funkcja `fetch`, zwraca obietnicę zasobu, który udostępnia funkcje
`text()` oraz `json()`, które po wywołaniu zwracają następną obietnicę.

Wynikiem funkcji `async` jest funkcja, która zwraca obietnicę, więc można jej użyć w ten sposób:

{% highlight javascript %}
total('jan').then(function(total) {
   console.log('jan ma ' + total);
});
{% endhighlight %}

Można oczywiście użyć, tej funkcji, razem ze słowem `await`, w innej funkcji `async`.

Tak naprawdę funkcja `async`, nie musi mieć w sobie słowa kluczowego `await`, a i tak będzie zwracała obietnicę, np:

{% highlight javascript %}
async function getUsername(user) {
    return user.username;
}

var person = {
    username: 'Jan'
};

getUsername(person).then(function(username) {
    console.log(username);
});
{% endhighlight %}

Zadziała także pusta funkcja. Po prostu obietnicą, będzie wartość `undefined`, ponieważ w języku JavaScript, każda
funkcja, która nie zwraca jawnie wartości, będzie zwracała wartość `undefined`.


Jako bardziej rozbudowany przykład, przypomnijmy sobie
[kod z poprzedniej części o obietnicach](/2018/05/asynchronicznosc-obietnice.html):


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

Mamy tu tablice użytkowników, a każdy z nich ma tablicę produktów. Z kolei każdy produkt, ma cenę oraz liczność.
Każda z tych wartości jest pobierana poprzez funkcję, która zwraca obietnicę. Powyższy kod, jako funkcja `async`,
wyglądałby tak:

{% highlight javascript %}
async function displayUsersTotal() {
    try {
        var users = await getUsers();
        for (let user of users) {
            user.products = await getProducts(user.id);
            for (let product of user.products) {
                product.price = await getPrice(product.id)
                product.count = await getQuantity(user.id, product.id);
            }
            user.total = user.products.reduce(function(acc, product) {
                return acc + (product.price * product.count);
            }, 0);
            console.log(user.name + ' ' + user.total);
        }
    } catch (e) {
        console.log('Błąd w którejś obietnicy, nigdy się nie wywoła');
    }
}
{% endhighlight %}

Jak widzicie kod jest o wiele krótszy i o wiele bardziej czytelny. W powyższym kodzie, oprócz `async/await`, użyłem
słowa kluczowego `let` oraz pętli `for..of`. `Let` działa tak jak `var`, tylko że zasięg zmiennej znajduję się wewnątrz
bloku, w którym została zdefiniowana, czyli poza instrukcja `for`, będzie niezdefiniowana oraz każda iteracja
pętli będzie miała swoją zmienną. Natomiast pętla `for..of` to nowy dodatek do języka JavaScript, dzięki któremu
iterując po tablicy, iterujemy po jej wartościach, a nie tak jak w przypadku `for..in` po kluczach/indeksach.

Jeśli funkcja, która wywoływana jest ze słowem kluczowym `async`, się nie powiedzie (wywoła się funkcja `reject`
obietnicy), wyrzucony zostanie zwykły wyjątek. Dlatego wystarczy jedna instrukcja `try..catch`, aby je
przechwycić. Tak jak w przypadku samych obietnic wystarczy jedno miejsce obsługi błędów.


Dla porównania [kod z funkcjami zwrotnymi, z pierwszej części](/2018/05/asynchronicznosc-obietnice.html):

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

Słowo kluczowe `await`, może być wywoływane, tylko i wyłącznie wewnątrz funkcja `async`. Ale istnieje
[propozycja aby dodać możliwość użycia go bez async](https://github.com/tc39/proposal-top-level-await). Z chwilą
pisania tego artykułu, wiem tylko o jednej możliwości użycia `await` poza funkcja `async`. Można go użyć w konsoli
devtools przeglądarki Google Chrome/Chromium.  Dlatego aby skorzystać z `await` "luzem", trzeba utworzyć IIFE
(ang. Immediately Invoked Function Expression), czyli funkcję anonimową, którą od razu wywołujemy:

{% highlight javascript %}
(async function() {
   var username = 'jan';
   var res = await fetch('/users/' + username);
   var user = await res.json();
   console.log(user.fullName);
})();
{% endhighlight %}

Można też użyć funkcji strzałkowej:

{% highlight javascript %}
(async () => {
   var username = 'jan';
   var res = await fetch('/users/' + username);
   var user = await res.json();
   console.log(user.fullName);
})();
{% endhighlight %}

Async/Await obsługuje większość nowoczesnych przeglądarek (oprócz oczywiście IE). Ich listę możesz zobaczyć na
[can I use](https://caniuse.com/#feat=async-functions).
