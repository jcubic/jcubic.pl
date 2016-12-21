---
layout: post
title:  "Wyrażenia Regularne"
date:   2016-04-10 22:04:28 +0200
categories: 
tags:  javascript regexp functions replace split match
author: jcubic
---

Wyrażenia regularne są małym mini językiem dostępnym niemal w każdym języku programowania.
JavaScript także udostępnia te wyrażenia. W tym artykule przedstawię pokrótce jak korzystać
z wyrażeń regularnych w języku JavaScript.

<!-- more -->

## Wyrażenia

Wyrażenie regularne można utworzyć na dwa sposoby:

* bezpośrednio za pomocą znaków slash np.

{% highlight javascript %}
/foo/g
{% endhighlight %}

* lub za pomocą konstruktora RegExp

{% highlight javascript %}
new RegExp("foo", "g");
{% endhighlight %}

w obu przypadkach utworzono proste wyrażenie, które będzie wskazywać wyrażenie "foo" z flagą globalną,
która ma znaczenie w przypadku użycia wyrażenia wraz z metodami match oraz replace.

## Metody

Z wyrażeniami regularnymi związane jest kilka metod dostępnych w objekcie string takich jak:
split, replace i match, w tych metodach jako argumentu oprócz wyrażenia regularnego można użyć łańcucha znaków.

* **split** - służy do dzielenia łańcucha znaków na części, metoda split zwraca tablicę,
* **replace** - służy do zamiany pewnego łańcucha na inny, w zamienniku mogą wystąpić wyrażenia `$1` do `$n` które wskazują grupę dopasowania, np.: `string.replace(/foo/g, 'bar')`, drugim argumentem oprócz ciągu znaków może także być funkcja w w której zwrócimy ciąg na który chcemy zamienić to co jest w pierwszym argumencie,
* **match** - służy do dopasowania łańcucha do wzorca zwraca tablicę znalezionych dopasowań.

## Składnia wyrażeń

Wyrażenia regularne to mini język, który składa się poniższych elementów

### Kwantyfikatory

* `*` kwantyfikator wskazujący, że to co jest przed ma być powtórzone 0 lub n razy,
* `+` kwantyfikator wskazujący, że to co jest przed ma być powtórzone 1 lub n razy,
* `?` kwantyfikator wskazujący, że to co jest przed ma być opcjonalne, występować 0 lub 1 raz,
* `{}` kwantyfikator wskazujący że coś ma być powtórzone `{1}` jeden raz `{1,3}` od 1 do 3 razy, `{5,}` przynajmniej 5 razy.

### Inne Znaki Specjalne

* `[]` grupa znaków, dopasowane będzie jeden ze znaków, które znajdują się w nawiasach lub znaki, które nie znajdują się w nawiasie gdy użyto znaku `^`, znaki mogą być oddzielone znakiem minus aby utworzyć zakres znaków np. `/[0-9]/` określa dowolną cyfrę lub `/[^0-9]/` dowolny znak nie będący cyfrą,
* `^` wskazuje początek łańcucha może także wskazywać miejsce za nową linią gdy podano flagę m - multiline, służy także do negacji grupy,
* `$` wskazuje koniec łańcucha, może także wskazywać miejsce przed nową linia gdy podano flagę m,
* `|` wskazuje znak lub np.: `/foo|bar/` określa ciąg znaków foo lub bar,
* `()` określa grupę dopasowania,
* `.` dopasowuje dowolny znak,
* `\n` nowa linia,
* `\t` znak tabulacji,
* `\w` dowolny znak będący częścią słowa,
* `\W` dowolny znak nie będący częścią słowa,
* `\d` dowolny znak będący cyfrą,
* `\D` dowolny znak nie będący cyfrą,
* `\s` dowolny biały znak taki jak spacja, tabulacja lub nowa linia,
* `\S` dowolny znak nie będący białym znakiem.

Aby użyć znaku specjalnego w dopasowaniu, jako znak a nie znak specjalny, należy poprzedzić go znakiem odwrotnego ukośnika (ang. backslash) np.: `/\[\]/` dopasowane będzie do nawiasów kwadratowych.

### Grupy
Za pomocą nawiasów okrągłych możemy tworzyć grupy. W przypadku metody match wynikowa tablica będzie zwierać całe dopasowanie pod indeksem 0 pod indeksami od 1 znajdować się będą dopasowania ciągów występujących w grupach np.:

{% highlight javascript %}
'foo 100'.match(/foo ([0-9]+)/);
{% endhighlight %}
zwróci tablicę `['foo 100', '100']`

dodatkowo za pomocą nawiasów można utworzyć:

* `(?:)` grupę, która nie wystąpi w wyniku, np. gdy chcemy dodać kwantyfikator np.: `/(?:foo)*/` będzie dopasowane do słowa foo powtórzonego 0 lub n razy,
* `(?=)` wskazuje pozytywne dopasowanie do przodu, to co znajdzie się w nawiasie będzie dopasowane ale nie znajdzie się w wyniku np.: `/foo(?=bar)/` będzie dopasowane do łańcucha foo ale tylko jeśli zaraz za nim będzie słowo bar,
* `(?!)` wskazuje negatywne dopasowanie do przodu, to co znajduje się w nawiasie nie może wystąpić w ciągu znaków oraz tak jak w przypadku dopasowania w przód wyrażenie nie znajdzie się w wyniku np.: `/foo(?!baz)/` będzie dopasowane do łąńcucha foo ale tylko jeśli nie znajdzie się za mnim słowo baz.

## Przykłady

### Wyrażenie regularne, które będzie dopasowane do adresów email:

{% highlight javascript %}
email.match(/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/);
{% endhighlight %}
za pomocą powyższego użycia match można sprawdzić czy ciąg znaków jest poprawnym adresem email.
Jest to dość proste wyrażenie, które nie sprawdza dokładnie adresu.

### Wyrażenie regularne, którego można użyć aby wyszukać telefonów w ciągu znaków:

{% highlight javascript %}
text.match(/\([0-9]{2}\) [0-9 ]{6,}[0-9]/g);
{% endhighlight %}

### Usunięcie nawiasów wokół wszystkich liczb

{% highlight javascript %}
text.replace(/\(([0-9]+)\)/g, '$1');
{% endhighlight %}

$1 wskazuje to co jest w nawiasie czyli ciąg cyfr (co najmniej 1).
