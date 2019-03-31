---
layout: post
title:  "Kolejne 10 pytań na rozmowę rekrutacyjną z języka JavaScript"
date:   2019-03-31 14:58:55+0200
categories:
tags:  javascript praca
author: jcubic
description: Oto kolejne 10 pytań jakie bym zadał na rozmowie rekrutacyjnej z języka JavaScript, czyli najważniejsza część Front-Endu. Przygotuj się na rozmowę kwalifikacyjną z JS.
image:
 url: "/img/javascript-interview.jpg"
 alt: "Grafika wektorowa przedstawiająca osobę z laptopem i logo JavaScript"
 width: 800
 height: 464
related:
  -
    name: "15 Pytań na rozmowę rekrutacyjną z React.js"
    url: "/2018/10/pytania-rekrutacyjne-z-react.js.html"
  -
    name: "15 Pytań na rozmowę rekrutacyjną z CSS"
    url: "/2018/10/pytania-rekrutacyjne-css.html"
  -
    name: "5 pytań na rozmowę rekrutacyjną z języka JavaScript"
    url: "/2017/09/pytania-na-rozmowe-rekrutacyjna-javascript.html"
---

Pytania na rozmowę rekrutacyjne były bardzo popularne. Do [CSS](/2018/10/pytania-rekrutacyjne-css.html) i
[React](/2018/10/pytania-rekrutacyjne-z-react.js.html) było po 15 pytań, a tych do języka
[JavaScript było tylko 5](/2017/09/pytania-na-rozmowe-rekrutacyjna-javascript.html). Postanowiłem więc napisać jeszcze
10 pytań jakie bym zadał na rozmowie kwalifikacyjnej na stanowisko Front-End developer.

Sprawdź czy znasz odpowiedź na każde z nich.

<!-- more -->

## 1. Jak można zmienić kontekst this metody/funkcji?

można użyć trzech metod dostępnych dla funkcji (metod dla obiektów funkcji): `bind`, `call` oraz `apply`.

## 2. Czym różnią się `let`, `const` i `var`?

`var` deklaruje zmienną dla całej funkcji, natomiast `let` oraz `const` dla bloku.

{% highlight javascript %}
for (var i = 0; i < 10; ++i);
console.log(i);
// 10

for (let j = 0; j < 10; ++j);
console.log(j);
// Uncaught ReferenceError: j is not defined
{
    let x = 10;
    console.log(x);
    // 10
}
console.log(x);
// Uncaught ReferenceError: x is not defined
{% endhighlight %}

Różnica między `const` a `let` jest taka, że `const` nie może być ponownie przypisane.  Chociaż nie oznacza to stałej.
Ponieważ gdy wartością będzie obiekt może się on zmienić (mutować).

{% highlight javascript %}
const foo = 10;
foo = 20;
// Uncaught TypeError: Assignment to constant variable.
const bar = {};
bar.baz = 10;
console.log(bar);
// {baz: 10}
{% endhighlight %}

## 3. Jakie są różnice między funkcją strzałkową od normalną?

Funkcje strzałkowe (ang. arrow function) dostają kontekst `this` z pierwszej zwykłej funkcji nadrzędnej lub jest
to obiekt window, gdy nie ma żadnej zwykłej funkcji w łańcuchu zakresów.

{% highlight javascript %}
var x = 10;
const foo = () => this.x;
console.log(foo());
// 10

const bar = {
    x: 10,
    baz: function() {
        return [1,2,3].map((x) => this.x + x);
    }
};
console.log(bar.baz());
// [11, 12, 13]
{% endhighlight %}

Funkcja strzałkowa może też występować w dwóch wersjach:

* skróconej (wyrażeniowej)

{% highlight javascript %}
foo = () => 10;
{% endhighlight %}

gdzie wynikiem wywołania funkcji jest pojedyncze wyrażenie

* rozszerzonej (blokowej)

{% highlight javascript %}
foo = (x) => { console.log(x); return x; }
{% endhighlight %}

Gdzie mamy wszystko to co w zwykłej funkcji

Dodatkowo jeśli mamy tylko jeden parametr, można pominąć nawiasy:

{% highlight javascript %}
const square = x => x * x;
{% endhighlight %}

Warto też dodać, że aby zwrócić pojedynczy obiekt używając skróconej wersji, trzeba użyć nawiasów:

{% highlight javascript %}
const obj = arg => ({x: arg});
console.log(obj(10));
// {x:10}
{% endhighlight %}

ponieważ gdy nie użyjemy nawiasów, będzie to blok z etykietą `x`, a wynikiem wywołania funkcji będzie
`undefined`.

{% highlight javascript %}
const obj = arg => {x: arg};
console.log(obj(10));
// undefined
{% endhighlight %}

## 4. Jak użwać tablicy jak stosu oraz kolejki?

Ciekawe pytanie, które pokazuje także, że znasz struktury danych.

1. Stos (dodaje na koniec, pobiera z końca - czyli LIFO)
  * dodawanie: `a.push(10);`
  * zdejnowanie: `a.pop();`

2. Kolejka (dodaje na koniec, pobiera z początki - czyli FIFO)
  * dodawanie: `a.push(10);`
  * zdejmowanie: `a.shift();`

## 5. Co wyświetli poniższy kod?

{% highlight javascript %}
for (let item of ['foo', 'bar', NaN, 'baz', NaN]) {
    if (item === NaN) {
        console.log('Not a number');
    }
}
{% endhighlight %}

Nie wyświetli się nic ponieważ, `NaN !== NaN`. Aby sprawdzić czy wartość jest `NaN`, należy użyć funkcji `isNaN` lub
lepiej `Number.isNaN`. Ponieważ funkcja `isNaN` zwróci `true` dla wartości `isNaN('x')`, dzieje się tak dlatego, gdyż
konwertuje ona wartość do liczby przed sprawdzaniem (tak jakby używała `parseInt`).

## 6. Wymień metody operacji na plikach w JavaScript (w przeglądarce)

Dostępne są:

* FileReader API - umożliwia czytanie plików tekstowych i binarych dodanych do elementu file (html
  `<input type="file"/>`) lub za pomocą drag & drop.  W niektórych przeglądarkach można też upuszczać cały
  katalog (zobacz [wsparcie](https://caniuse.com/#feat=filereader))

* Filesystem API - umożliwia utworzenie przestrzeni na dysku i zapis w niej plików.
  [Wsparcie jest dość ograniczone](https://caniuse.com/#feat=filesystem)

* Ajax - jest to podstawowe narzędzie gdy chcemy czytać i zapisywać pliki, działa tylko z serwerem i to on
  musi obsłużyć czytanie i pisanie i zwracać wynik do przeglądarki.

## 7. Co wyświetli poniższy kod?

{% highlight javascript %}
var x = 10;
delete x
console.log(x);
{% endhighlight %}

Wyświetli `10` poniewarz operator `delete` działa tylko dla pól obiektów.

## 8. Co to znaczy że funkcje są typem pierwszo klasowym?

Typ pierwszo klasowy oznacza, że funkcje można używać wszędzie tam, gdzie inne wartości. Czyli można je:

* utworzyć jako anoniowe wyrażenie
* przypisać do zmiennej
* użyć jako elementu struktury danych
* porównywać z innymi obiektami
* przekazywać jako argumenty funkcji
* zwracać jako wynik funkcji

## 9. Jakie problemy może sprawić poniższy kod odwracający ciąg znaków?

{% highlight javascript %}
function reverse(str) {
    return str.split('').reverse().join('');
}
{% endhighlight %}

Problemem jest sposób w jaki JavaScript zapisuje znaki Unicode. Niektóre znaki składają się z kilku
zakodowanych punktów (ang. code points). Są to tzw. pary surogatów lub znaki emoji, które mogą się składać
nawet z 3 zakodowanych punktów. Mogą się też zdarzyć znaki, które dodają dodatkowe ozdobniki np. akcenty do
zwykłych liter, których może być więcej niż jeden.

np.:

{% highlight javascript %}
"mañana".length;
// 7
reverse("mañana")
// "anãnam"
{% endhighlight %}

ponieważ słowo zapisane jest jako: 'ma\u{006E}\u{0303}ana'

{% highlight javascript %}
'💩'.length;
// 2
reverse('💩')
// "��"
{% endhighlight %}

Więcej o kodowaniu znaków Unicode w artykule:
["JavaScript has a Unicodeproblem"](https://mathiasbynens.be/notes/javascript-unicode) Mathiasa Bynensa lub jak ktoś
woli jego wystąpienie ["JavaScript ♥ Unicode" na konferencji JSConf](https://www.youtube.com/watch?v=zi0w7J7MCrk).

## 10. Co wyświetli poniższy kod i dlaczego?

{% highlight javascript %}
function foo() {
    return foo;
}
console.log(new foo() instanceof foo);
{% endhighlight %}

Wyświetli się `false`, ponieważ funkcja/konstruktor zwraca nowy obiekt, tylko gdy funkcja nic nie zwraca
(zwraca `undefined`). Gdy natomiast zwracana jest jakaś wartość będzie ona wynikiem wywołania tej funkcji z
operatorem `new`.  Operator `instanceof` sprawdza czy wartość jest instancją konstruktora. W tym przypadku nie
będzie to obiekt tylko funkcja/konstruktor foo (chociaż funkcje w JavaScript to też obiekty).
