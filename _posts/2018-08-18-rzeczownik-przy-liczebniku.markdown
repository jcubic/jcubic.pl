---
layout: post
title:  "Rzeczownik przy liczebniku w języku Polskim w JavaScript"
date:   2018-08-18 12:13:39+0200
categories:
tags:  javascript api i18n
author: jcubic
description: Do języka JavaScript weszło nowe API, które dodało obsługę rzeczowników przy odpowiednich licznościach.
image:
  url: "/img/book-grass.jpg"
  alt: "Zdjęcie książki na trawie"
---

Do języka JavaScript weszło nowe API, pod przestrzenią nazw `Intl`, które między innymi
dodało obsługę liczb mnogich w różnych językach.  Chodzi i odmianę rzeczowników przy
odpowiednich licznościach.

<!-- more -->

Aby użyć tego API tworzymy nową instancje klasy `Intl.PluralRules` i wywołujemy metodę
`select` z wartością liczbową. Funkcja ta zwraca ciąg znaków. Dla języka Polskiego mamy
trzy wartości: "many", "one" oraz "few". Poniżej kod, który służy do wyświetlania liczby
oraz odpowiedniej formy rzeczownika jabłko.

{% highlight javascript %}
var arr = new Array(100).fill(0);
var strings = {many: 'jabłek', one: 'jabłko', few: 'jabłka'};
var pr = new Intl.PluralRules('pl-PL');
for (var i in arr) {
    console.log(i + ' ' + strings[pr.select(i)]);
}
{% endhighlight %}

Matematyka za obliczeniami odpowiedniej formy, jest taka sama jakiej używa się w bibliotece
[gettext](https://www.gnu.org/software/gettext/), czyli:

{% highlight javascript %}
(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)
{% endhighlight %}

Jeśli chodzi o język polski to ciąg znaków "few" dla liczby 94 może nie jest do końca
trafny, ale nie mniej dodatek jest użyteczny.  Może to nie jest jakieś strasznie
skomplikowane, aby obliczać to samemu, ale kod może się dość skomplikować, jeśli zajdzie
potrzeba użycia kilku języków. Dodatkowo nie trzeba szukać odpowiedniej formuły dla
każdego języka.

Ciąg znaków przekazywany do konstruktora `Intl.PluralRules` jest w formacie
[BCP 47](https://en.wikipedia.org/wiki/IETF_language_tag). Więcej o tym API można znaleźć
na stronie
[MDN](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/PluralRules).
