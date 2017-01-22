---
layout: post
title:  "Jak używać biblioteki jQuery"
date:   2017-01-22 18:01:42+01:00
categories:
tags:  css jQuery javascript
author: jcubic
---

Biblioteka jQuery, mimo wzrastającej popularności takich frameworków jak [Angular](https://pl.wikipedia.org/wiki/AngularJS) czy [React](https://en.wikipedia.org/wiki/React_(JavaScript_library)), które umożliwiają tworzenie skomplikowanych aplikacji typu [SPA](https://en.wikipedia.org/wiki/Single-page_application), nadal jest najczęściej wykorzystywaną biblioteką JavaScript na stronach internetowych. Warto więc wiedzieć jak jej używać. W tym poście przedstawię jak korzystać z biblioteki jQuery.

<!-- more -->

## Dodawanie biblioteki do strony

Aby użyć biblioteki jQuery najpierw trzeba ją dodać do strony, w tym celu można dodać poniższy kod do naszej strony internetowej.

{% highlight html %}
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
{% endhighlight %}

Powyższy kod doda najnowszą, z chwilą pisania tego artykułu, wersje jQuery, czyli 3.1.1. Aby w pełni móc skorzystać z biblioteki nasz kod dodajemy jak funkcję zwrotną DOM ready:

{% highlight javascript %}
$(document).ready(function() {
    // nasz kod
});
{% endhighlight %}

lub jego skróconą wersję czyli:

{% highlight javascript %}
$(function() {
    // nasz kod
});
{% endhighlight %}

Jeśli nasza strona korzysta z jakieś innej biblioteki, która używa znaku dolara możemy używać biblioteki jQuery w trybie no conflict

{% highlight javascript %}
jQuery.noConflict();

jQuery(function($) {
    // nasz kod
});
{% endhighlight %}

Mamy wtedy pewność, że nasza strona jest w pełni załadowana (mamy dostęp do DOM-u strony) i możemy zacząć ją manipulować. W dalszych przykładach pominę funkcje DOM ready.

## Główny element biblioteki

Główną częścią biblioteki jQuery jest funkcja `$` lub `jQuery`, która może przyjmować funkcje, jak w przykładzie powyżej, selektor css, kod html lub jeden, lub więcej elementów DOM. Jeśli argumentem jest ciąg znaków, a jego pierwszym znakiem jest `<` wtedy biblioteka jQuery utworzy Fragment DOM, który będzie można dodać do strony np.:

{% highlight javascript %}
$('<div>Hello World!</div>').appendTo('body');
{% endhighlight %}

Powyższy kod utworzy nowy element div z tekstem `Hello World!` i doda go do elementu body strony.

Jeśli natomiast do funkcji `$` przekażemy ciąg znaków, który jest poprawnym selektorem CSS, wtedy biblioteka wyszuka taki element na stronie i wynikiem będzie obiekt `jQuery`, a dokładnie obiekt `jQuery.fn.init`, który będzie zawierał wszystkie elementy pasujące do danego selektora np.:

{% highlight javascript %}
$('#item');
{% endhighlight %}

zwróci element o atrybucie `id="item"` (na stronie powinien znajdować się tylko jeden element z danym id ale biblioteka jQuery zwróci wszystkie elementy, które mają ten atrybut z podaną wartością) to samo można osiągnąć wywołując natywny kod JavaScript poniżej.

{% highlight javascript %}
document.getElementById('item');
{% endhighlight %}

natomiast poniższy kod:

{% highlight javascript %}
$('.item');
{% endhighlight %}

zwróci wszystkie elementy o klasie item np. dla powyższego kodu html:

{% highlight html %}
<ul>
  <li class="item">Jeden</li>
  <li class="item">Dwa</li>
  <li class="item">Trzy</li>
</ul>
{% endhighlight %}

zwróci trzy elementy `li`. To samo można osiągnąć  używając kodu JavaScript:

{% highlight javascript %}
document.getElementsByClassName('item');
{% endhighlight %}

Więcej o selektorach CSS także CSS3 można znaleźć w internecie np. na stronie [kurshtml.edu.pl](http://www.kurshtml.edu.pl/css/selektory.html). W nowoczesnych przeglądarkach znajduje się funkcja, która może zastąpić selektory biblioteki jQuery, a mianowicie `document.querySelector`, która zwraca pierwszy element o danym selektorze lub `document.querySelectorAll`, która zwróci obiekt tablico podobny NodeList, po który można iterować:

{% highlight javascript %}
var item = document.querySelector('#item');
var items = document.querySelectorAll('.item');
{% endhighlight %}

## Funkcje jQuery

Obiekt jQuery zwrócony po wywołaniu funkcji dolara zawiera szereg metod:

### Metody służące do przechodznie po elementach obiektu

* .next() - zwróci następny element,
* .prev() - zwróci poprzedni element,
* .siblings() - zwróci wszystkie rodzeństwo elementu,
* .children() - zwróci wszystkie dzieci danego elementu,
* .parent() - zwróci rodzica elementu,
* .parents() zwróci listę wszystkich rodziców danego elementu tj. pierwszym elementem będzie element parent, kolejnym parent parent itd.,
* .find(selektor) - zwróci wszystkie elementy pasujące do nowego selektora przekazanego jako argument które są dziećmi przedniego elementu,
* .not(selektor) - zwróci elementy które nie należą dod danego selektora, np: `$('div').not('.foo')` zwróci elementy div które nie mają klasy foo,
* .eq(indeks) - zróci pojedynczy element o indeksie podanym jako argument, np jeśli selektor zwraca dwa elementy to `eq(1)` zwróci drugi element.

### Metody służące do manipulacji dom

* append(string) - metoda doda zawartość przekazaną jako argument (może być kod html lub selektor) do danego elementu,
* appendTo(selektor) - metoda doda obiekt jQuery do elementu o danym selektorze,

{% highlight javascript %}
$('div').append('<span>Hej</span>');
$('<span>Hej</span>').appendTo('div');
{% endhighlight %}

Oba powyższe przykłady zrobią to samo dodadzą span Hej do każdego elementu div na stronie.

* .prepend(string) - metoda podobna do append tylko element dodawany jest jako pierwsze dziecko danego elementu,
* .prependTo(selektor) - metoda działa analogicznie do appendTo tylko element dodawany jest ko pierwsze dziecko tak jak prepend,

* .remove() - metoda usunie element z dokumentu,

* .replaceWith(string) - metoda zamieni dane elementy nowymi elementami przekazanymi jako argument,

* .hide()/.show() - metody ustawiają styl inline display: none lub display: block/inline w zależności od elementu,
* .css() - ustawia dany styl inline, można przekazać do tej funkcji dwa argumenty nazwę właściwości i wartość lub obiekt, np.:
{% highlight javascript %}
$('div').css('color', 'red');
$('span').css({
  border: '1px solid green',
  background: 'blue'
{% endhighlight %}

* .val() - metoda zwraca wartość elementu formularza tj. input, textarea lub select, lub ustawia nową wartość danego elementu,
* .data() - metoda ustawia lub pobiera wartość atrybutu `data-nazwa`, w przypadku zapisywania wartości, wartość atrybutu się nie zmiania. Aby zmienić atrybut należy użyć `.attr()`, która przyjmuje jeden string (do pobrania atrybutu) dwie wartości do zapisywania lub obiekt także do zapisywania,
* .removeAttr() - metoda służy do usuwania atrybutu,
* .addClass()/.removeClass() - metody dodają/usuwają klasę do/z wybranych elementów.

### Zdarzenia

Biblioteka jQuery umożliwia dodawania uchwytów (ang. handlers) zdarzeń. Główną metodą do dodawania zdarzeń jest metoda `.on()` a do usuwania `.off()` ale biblioteka jQuery zawiera także skróty do dodawania zdarzeń np: `.click()` czy `.keypress()`

{% highlight javascript %}
$('button').click(function() {
    alert('Kliknąłeś przycisk');
});
{% endhighlight %}


Jeśli chcemy dodać zdarzenie do elementów, które mogą się pojawić później, musimy użyć delegacji zdarzeń, np.:

{% highlight javascript %}
$('ul').on('click', 'li', function() {
   alert('cliknąłeś na element li');
});
$('ul').append('<li>Nowy element</li>');
{% endhighlight %}

Jeśli klikniemy na nowy element zostanie wywołana nasza funkcja ponieważ uchwyt zdarzenia został dodany do elementu ul.

## Wywoływanie łańcuchowe

Każda metoda biblioteki jQuery, która nie zwraca wartości zwraca obiekt jQuery, nowy lub poprzedni, dlatego jest możliwe wywoływanie łańcuchowe (ang. chain):

{% highlight javascript %}
$('div').css('color', 'red').find('.item:eq(1)').hide().next().show();
{% endhighlight %}

## Animacje

Do animacji służy funkcja animate np.:

{% highlight javascript %}
$('foo').animate({
    width: '250px'
});
{% endhighlight %}

Można animować właściwości css, aby animować kolor (właściwość color lub background-color) należy użyć pluginu [jQuery Color](https://github.com/jquery/jquery-color).

## Pętle

Do iterowania po elementach służy metoda `.each()`:

{% highlight javascript %}
$('foo').each(function() {
  $(this).find('span').css('color', 'blue');
});
{% endhighlight %}

można także użyć metody .map(), która tak jak metoda obiektu `Array` w ES5 zwraca nowy obiekt jQuery, aby np. dostać tablicę atrybutów href linków, można użyć poniższego kodu:

{% highlight javascript %}
var links = $('a').map(function() {
  return $(this).attr('href');
}).get();
{% endhighlight %}

Należy użyć metody get aby otrzymać tablicę, metoda `.map()` zwraca obiekt jQuery.

## Ajax

Oprócz funkcji dostępnych jako metody obiekty jQuery, biblioteka udostępnia także funkcje statyczne dodane do obiektu dolara, takie jak np. funkcje do wykonywania zapytań HTTP. Główną funkcją do wykonywania takich zapytań jest funkcja `$.ajax`, ale biblioteka zawiera także skróty `$.get` oraz `$.post`.

{% highlight javascript %}
$.get('strona.html', function(strona) {
   $('.main').replaceWith(strona);
});
{% endhighlight %}

Powyższy kod wykona zapytanie AJAX-owe typu GET i zamieni zawartość elementu `.main` tym, co dostanie z serwera.

## Rozszerzanie biblioteki

Bibliotekę jQuery można rozszerzać o nowe metody, tzw. plug-iny, prawdopodobnie dlatego jest tak popularna. Jest bardzo dużo gotowych plug-inów, które można używać. Aby utworzyć nowy plugin, należy dodać nową właściwość do obiektu `$.fn` np:

{% highlight javascript %}
$.fn.link = function(options) {
   options = options || {};
   return this.each(function() {
      var self = $(this);
      if (options.title) {
          self.attr('title', options.title);
      }
      if (option.href) {
          self.attr('href', options.href);
      }
   });
};
{% endhighlight %}

I można użyć tak:

{% highlight javascript %}
$('a').link({href: 'http://example.com', title: 'Example Page'});
{% endhighlight %}

Powyższy kod wykona naszą metodę link na każdym elemencie a czyli doda atrybut `href` i `title` do każdego linku.

Innym sposobem rozszerzania biblioteki są własne customowe selektory, np. aby dodać selektor `:len()`, który pobierze tylko te elementy, których długość tekstu jest mniejsza od podanej, możemy użyć poniższego kodu:

{% highlight javascript %}
$.expr[':'].len = function(obj, index, meta, stack) {
    var self = $(obj);
    var text = self.text();
    var len = parseInt(meta[3]);
    return text.length < len;
};
{% endhighlight %}

I wywołać:

{% highlight javascript %}
$('div:len(10)').css('color', 'red');
{% endhighlight %}

Powyższy kod ustawi kolor czerwony dla elementów div, których tekst jest mniejszy niż 10 znaków.




Listę wszystkich metod oraz funkcji można znaleźć na [stronie api projektu](http://api.jquery.com/).

*[DOM]: Document Object Mode
*[HTTP]: Hypertext Transfer Protocol
*[CSS]: Cascading Style Sheets
*[AJAX]:  Asynchronous JavaScript and XML
*[SPA]: Single Page Application
*[ES5]: EcmaScript 5
