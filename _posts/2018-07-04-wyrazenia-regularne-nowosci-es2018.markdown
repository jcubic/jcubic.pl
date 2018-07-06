---
layout: post
title:  "Co Nowego w Wyrażeniach Regularnych"
date:   2018-07-04 20:54:28+0200
categories:
tags: javascript regexp es2018
author: jcubic
---

Standard ES2018 czyli ES9 został zatwierdzony. W ramach tej wersji weszło kilka nowy funkcjonalności do wyrażeń regularnych czyli regexów (od angielskiego Regular Expressions).

<!-- more -->

* Flaga s

Aby dopasować dowolny znak używa się operatora kropki. Nie zadziała ona jednak gdy chcemy
dopasować znak do znaku nowej linii.  Aby obejść to ograniczenie najczęściej stosowano
takie wyrażenie `[\s\S]`, które oznacza dowolną spacje lub nie spacje czyli dowolny znak.
Nie jest to jednak bardzo czytelne i może dodatkowo komplikować wyrażenia. Z pomocą
przychodzi nowa flaga `s`, która określa że znak kropki dopasowuje się do dowolnego
znaku. Czyli jest to "Dot All" znany z innych języków.

* Flaga u

Jest to flaga oznaczająca Unicode, która jeśli użyta udostępnia, wewnątrz wyrażenia:

* wyrażenie `\u{kod unicode}` np.:

{% highlight jsnext %}
'☃'.match(/\u{2603}/u);
{% endhighlight %}

* właściwości tekstu `\p{ }` (ang. Unicode property escape):

To nowy sposób dopasowywania do zakresu znaków. Działa podobnie jak wyrażenie `\u{}`, z
tym że możemy przekazywać nazwy określające jakie znaki chcemy dopasować, np:

{% highlight jsnext %}
/^\p{ASCII}+$/u.test('AABB');
// true
/^\p{Script=Hebrew}+\s\p{Script=Hebrew}+$/u.test('העלא וועלט');
// true
/^[\p{Letter}\p{White_Space}]+$/u.test('Γειά σου Κόσμε');
// true
{% endhighlight %}

Wielkość znaków właściwości ma znaczenie.

* flaga u wpływa także na operator kropki czyli dowolnego znaku:

{% highlight jsnext %}
var string = 'a💩b';

console.log(/a.b/.test(string));
// false
console.log(/a.b/u.test(string));
// true
{% endhighlight %}

Znak &#x1f4a9; (Pile of Poo) znajduje się w tzw. przestrzeni Symboli Astralnych
(ang. astral symbols), tzn. że w języku JavaScript ich kod zawiera dwa znaki, są to
tzw. pary surogatów (ang. surrogate pairs).

{% highlight jsnext %}
'💩'.length;
// 2
{% endhighlight %}

Dlatego bez flagi `u` trzeba by użyć dwóch kropek, albo użyć wyrażenia
`/a(.{1,2})b/`. Może to jednak być problem, gdy chcemy dopasować, w typ samym miejscu,
znaki składają się z jednego lub z dwóch znaków.

Więcej o Unicode, w języku JavaScript, możesz przeczytać w artykule Mathiasa Bynensa
(JavaScript has a Unicode problem)[https://mathiasbynens.be/notes/javascript-unicode].

* Nazwane grupy

Do tej pory można było pobierać grupy tylko za pomocą indeksów czyli:

{% highlight jsnext %}
var input = 'var foo = bar;';
var re = /var ([A-Za-z]+)\s*=\s*([^;]+);/;
var m = input.match(re);
console.log(`przypisanie ${m[1]} do zmiennej ${m[2]}`)
{% endhighlight %}

Jest to mało czytelne. Dodatkowo jeśli musimy dodać nową grupę na początku np. gdy musimy
pobrać `const`, `let` lub `var`, to musimy w każdym miejscu, gdzie była użyta grupa, dodać
do indeksu jeden. Rozwiązaniem tego problemu są grupy nazwane. Ich składnia wygląda tak
`(?<nazwa>wyrażenie)`, oto poprzedni przykład z nazwanymi grupami.

{% highlight javascript %}
var input = 'var foo = bar;';
var re = /var (?<name>[A-Za-z]+)\s*=\s*(?<value>[^;]+);/;
var m = input.match(re);
console.log(`przypisanie ${m.groups.value} do zmiennej ${m.groups.name}`)
{% endhighlight %}

* Asercje wsteczne (ang. Look Behind)

Asercje do przodu (ang. Look Ahead) są w Wyrażeniach Regularnych od dawna (może nawet od samego początku ich istnienia).

Ich składnia wygląda tak `(?=wyrażenie)` oraz `(?!wyrażenie)` np:

{% highlight jsnext %}
var re = /var ([A-Za-z]+)(?=\s*=\s*[0-9]+(?:.[0-9]+)?)/;
var input = 'var foo = 10;';
input.match(re);
// ["var foo", "foo", index: 0, input: "var foo = 10;", groups: undefined]
{% endhighlight %}

to wyrażenie będzie dopasowane do nazwy zmiennej ale tylko jeśli wartością będzie liczba,
która nie jest zawarta w wyniku.  Podobnie działa asercja negatywna (ang. Negative Look
Ahead).

Natomiast nowe są asercje wsteczne (ang. Look Behind), znane z wyrażeń regularnych w
innych językach takich jak java, PHP czy Python. Ich składnia też jest taka sama jak w
innych językach czyli `(?<=wyrażenie)` pozytywne wsteczne oraz `(?<!wyrażenie)` negatywne
wsteczne.

Przykład:

{% highlight jsnext %}
var re = /(?<!var[^=]\s*=\s*)([0-9]+(?:.[0-9]+)?);/;
'var foo = 10;'.match(re);
// null
'const foo = 20;'.match(re);
// ["20;", "20", index: 12, input: "const foo = 20;", groups: undefined]
{% endhighlight %}

Wparcie, dla poszczególnych dodatków, możecie zobaczyć w
[tabeli standardu ECMAScript](http://kangax.github.io/compat-table/es2016plus/).

* String::matchAll

To nowa funkcja, niestety nie zaimplementowana jeszcze w żadnej przeglądarce, oprócz
Chrome (z chwilą pisanie tego artykułu), ale trzeba ją włączyć ręczenie. Funkcja ta
upraszcza wielokrotne dopasowanie, przykład:

{% highlight jsnext %}
const regex = /\b\p{ASCII_Hex_Digit}+\b/gu;
const string = 'Ten tekst zawiera znaki DEADBEEF CAFE AAFFBB';
let match;
while (match = regex.exec(string)) {
   console.log(match);
}
{% endhighlight %}

ten kod zadziała ponieważ funkcja exec zachowuje indeks poprzedniego wywołania, ale tylko
gdy wyrażenie ma flagę g, inaczej będzie to nieskończona pętla. Ten kod można jednak
zastąpić funkcją matchAll:

{% highlight jsnext %}
const regex = /\b\p{ASCII_Hex_Digit}+\b/gu;
const string = 'Ten tekst zawiera znaki DEADBEEF CAFE AAFFBB';
for (const match of string.matchAll(regex)) {
   console.log(match);
}
{% endhighlight %}
