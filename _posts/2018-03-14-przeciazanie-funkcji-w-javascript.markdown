---
layout: post
title:  "Przeciążanie funkcji i metod w JavaScript"
date:   2018-03-14 09:02:11+0100
categories:
tags:  javascript funkcje
author: jcubic
description: JavaScript nie udostępnia przeciążania funkcji, oto jak można dodać taką funkcje do języka.
image:
  url: "/img/family.jpg"
  alt: "Rodzina 4 osobowa"
---

JavaScript jest językiem dynamicznym, w którym funkcje mogą przyjmować wiele argumentów. Nie ma w nim jednak
mechanizmu, który by wywoływał inne funkcje w zależności do liczby argumentów (czyli nie obsługuje przeciążania
funkcji). W tym wpisie przedstawię jak prosto można taki mechanizm dodać do języka.

<!-- more -->

Idea wzięła się od wpisu na blogu Johna Resiga (tego od jQuery)
[JavaScript Method Overloading](https://johnresig.com/blog/javascript-method-overloading/). W swojej implementacji
Jonh używał takiego API:

{% highlight javascript %}
function Users(){
  addMethod(this, "find", function(){
    // Find all users...
  });
  addMethod(this, "find", function(name){
    // Find a user by name
  });
  addMethod(this, "find", function(first, last){
    // Find a user by first and last name
  });
}

var users = new Users();
users.find(); // wyszukaj wszystkie
users.find("John"); // Wysuzkaj po imieniu
users.find("John", "Resig"); // Wyszukaj po imieniu i nazwsiku
users.find("John", "E", "Resig"); // nie zadziała
{% endhighlight %}

Poszedłem o krok dalej i napisałem funkcje, której można użyć, przekazując tablicę funkcji.
Zwraca ona nową, przeładowaną funkcje. Można jej użyć w ten sposób:

{% highlight javascript %}
var o = {
    x: 10,
    foo: method("foo", [
        function(a) {
            console.log("a: " + a);
        },
        function(a, b) {
            console.log("b: " + a + ' ' + b);
        },
        function() {
            console.log("c: " + this.x);
       }
    ])
};

o.foo(10);
o.foo(10, 20);
o.foo();
try {
    o.foo(1,2,3);
} catch (e) {
    console.error(e.message);
}
{% endhighlight %}

Ostatnie wywołanie zwróci wyjątek ponieważ nie ma funkcji z 3 argumentami.

Moja funkcja `method` wygląda tak:

{% highlight javascript %}
function method(name, fns) {
    if (fns instanceof Array) {
        if (fns.length == 1) {
            return fns[0];
        }
        // zamiast Array::reduce można zwracać funkcje z pętlą for
        return fns.reduce(function(result, fn) {
            return function() {
                var len = arguments.length;
                if (len == fn.length) {
                    fn.apply(this, arguments);
                } else if (typeof result == 'function') {
                    result.apply(this, arguments);
                } else {
                    throw new Error("Can't find method '" + name + "' with " + len +
                                    ' arg' + (len != 1 ? 's' : ''));
                }
            };
        }, null);
    } else {
        return fns;
    }
}
{% endhighlight %}

Nazwa `method` może nie jest najtrafniejsza, ponieważ zwracana jest zwykła funkcja i lepsza byłaby np. `overload`.

Funkcja korzysta z ciekawej właściwości języka JavaScript, gdzie każda funkcja posiada właściwość `length`, która
określa liczbę parametrów oraz wewnątrz funkcji `arguments.length`, która zawiera liczbę argumentów wywołania.

Można też funkcje uprościć i pobierać listę funkcji jako argumenty, wtedy funkcja wyglądałaby tak:

{% highlight javascript %}
function method(name) {
    var fns = [].slice.call(arguments, 1);
    if (fns.length == 1) {
        return fns[0];
    }
    return fns.reduce(function(result, fn) {
        return function() {
            var len = arguments.length;
            if (len == fn.length) {
                fn.apply(this, arguments);
            } else if (typeof result == 'function') {
                result.apply(this, arguments);
            } else {
                throw new Error("Can't find method '" + name + "' with " + len +
                                ' arg' + (len != 1 ? 's' : ''));
            }
        };
    }, null);
}
{% endhighlight %}

Zamiast `[].slice.call` można użyć operatora rest z nowej wersji ECMAScript czyli:

{% highlight javascript %}
function method(name, ...fns) {
    // jak wyżej
}
{% endhighlight %}

Możesz przetestować funkcje `method` w [demo na CodePen](https://codepen.io/jcubic/pen/wmaGRx?editors=0011).
