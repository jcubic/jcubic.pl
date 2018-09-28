---
layout: post
title:  "Programowanie Obiektowe w JavaScript"
date:   2015-10-06 17:25:36
categories:
tags:  javascript front-end functions objects oop
author: jcubic
description: Dzisiaj przedstawię wam jak programować obiektowo w języku JavaScript.
sitemap:
  lastmod: 2017-09-08 20:35:15+0200
---

Dzisiaj przedstawię wam jak programować obiektowo w języku JavaScript.

<!-- more -->

## Obiekty bezpośrednie

Najprostrzym sposobem utworzenia nowego obiektu jest jego utworzenie "inline" czyli tzw. Object literal:

{% highlight javascript %}
var jan = {
    age: 24,
    name: "Jan",
    lastname: "Kowalski"
    run: function() {
        console.log("I'm running");
    }
};
{% endhighlight %}

jest to przydatne jeśli potrzebujemy utworzyć tylko jedną instancję obiektu.

## Funkcje jako konstruktory

Drugim ze sposobów utworzenia obiektu w języku JavaScript jest użycie funkcji
jak konstruktora.

{% highlight javascript %}
function Person(age) {
    this.age = function() {
        return age;
    };
    this.run = function() {
        console.log('running');
    };
}
{% endhighlight %}

Wewnątrz konstruktora mamy dostęp do specjalnej zmiennej `this` która tak jak
w przypadku innych języków obiektowych daje dostęp do aktualnego obiektu.

Do utworzenia nowego obiektu korzystamy z operatora `new`.

{% highlight javascript %}
var person = new Person(24);
{% endhighlight %}

## Prototypy

Język JavaScript jest językiem prototypowym, nie ma w nich klas tak jak w przypadku
języków takich jak Java czy C++. Prototyp określa obiekt po jakim dziedziczą obiekty
utworzone za pomocą danego konstruktora.

{% highlight javascript %}
function Person() {
    this.run = function() {
        console.log("I'm running");
    };
}
function Student() {
    this.study = function() {
        console.log("I'm studying");
    };
}
Student.prototype = new Person();
var student = new Student();
student.run();
student.study();
{% endhighlight %}

W powyższym przykładzie prototypem obiektów Student jest obiekt Person, dlatego
instancje obiektu Student posiadają dostęp do metody run zdefiniowanej w obiekcie
Person. Obiekty utworzone za pomocą konstruktora Student dziedziczą po obiekcie Person.

Prototypy tworzą łańcuch (ang. chain). W momencie odwołania do właściwości lub metody
sprawdzane jest czy dana właściwość czy metoda dostępna jest w danym obiekcie, potem
sprawdzany jest ciąg prototypów aż do obiektu `Object`, jeśli dana właściwość lub
metoda nie zostanie znaleziona zwracana jest wartość `undefined`. W przypadku metody
będzie to wyjątek że nie można wywołać funkcji która jest `undefiend`.

Do prototypu można także dodawać nowe metody:

{% highlight javascript %}
Student.prototype.speak = function() {
    console.log("I'm speaking");
};
{% endhighlight %}

**UWAGA**: Z chwilą pisania tego artykułu istniał tylko jeden sposób definicji klas w JavaScript.
Poprzez dziedziczenie prototypowe. W ES6 doszło nowe słowo kluczowe class za pomocą istnieje
możliwość tworzenie klas tak jak to jest realizowane w C++ lub Java. Z drobnymi różnicami jest to tylko
cukier syntaktyczny na mechanizm prototypów.
