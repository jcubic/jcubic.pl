---
layout: post
title:  "Importy HTML"
date:   2017-09-16 11:56:22+0200
categories:
tags:  javascript html css
author: jcubic
description: Importy HTML to nowa funkcjonalność w html, która jest częścią web komponentów. Daje możliwość importowania skryptów JavaScript, szablonów CSS czy stron HTML oraz daje dostęp do ich zawartości.
---

Importy HTML (ang. HTML Imports) to nowa funkcjonalność w HTML, która jest częścią web komponentów. Daje możliwość importowania skryptów JavaScript, szablonów CSS czy stron HTML oraz daje dostęp do ich zawartości. Tak jak w przypadku AJAX-a, ale następuje to zanim strona się załaduje, w momencie gdy parser napotka tag importu w HTML.

<!-- more -->

W HTML istniało do tej pory kilka sposobów na ładowanie zewnętrznej treści:

* `iframe` - służą do dodawania zagnieżdżonych stron HTML.
* `link` - z jego pomocą można dodawać do strony arkusze CSS, fav-ikonki lub zlinkować Kanał RSS itp.
* `script` - służy do załadowania plików JavaScript jeśli użyto atrybutu `src`.
* `audio` - można za jego pomocą załadować pliki muzyczne.
* `video` - służy do dołączania plików wideo.
* `object` - stary sposób dołączania zewnętrznych treści.

Teraz do tego dochodzą jeszcze importy HTML. Aby dołączyć za ich pomocą zewnętrzny plik, używamy tagu link z atrybutem `rel="import"`:

{% highlight html %}
<link rel="import" href="import.html"/>
{% endhighlight %}

podana strona może zawierać pliki JavaScript i CSS np.:

{% highlight html %}
<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>
</html>
{% endhighlight %}

Jeśli dołączymy w naszym skrypcie taki import, załaduje on nam do strony bibliotekę jQuery. Importy są singletonami więc jeśli dołączymy kilka razy dany import będzie on pobrany tylko raz.

Dzięki importom HTML mamy możliwość definiowania komponentów, które dołączają wszystkie potrzebne pliki np. poniżej import który dołączy moją bibliotekę [jQuery Terminal](http://terminal.jcubic.pl) i utworzy globalną funkcje `terminal`:


{% highlight html %}
<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery.terminal@1.7.0/css/jquery.terminal.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/jquery.terminal@1.7.0/js/jquery.terminal.min.js"></script>
<style>
body {
    min-height: 100vh;
    margin: 0;
}
</style>
<script>
(function() {
    var $ = jQuery.noConflict();
    window.terminal = function(selector) {
        var args = [].slice.call(arguments, 1);
        return $.fn.terminal.apply($(selector), args);
    };
})();
</script>
</head>
<body>
</body>
{% endhighlight %}

Użytkownik nie musi wiedzieć że używamy biblioteki jQuery, wystarczy że wie że ma funkcje `terminal`, która przyjmuje selektor CSS jako pierwszy argument. Scope wewnątrz importu jest taki sam jak w dokumencie, który importuje plik, czyli `window` jest ten sam. Można tego importu użyć w ten sposób:

{% highlight html %}
<!DOCTYPE html>
<html>
<link rel="import" href="terminal.html"/>
<body onload="terminal('body')"></body>
</html>
{% endhighlight %}

Niestety jeśli chodzi o style CSS w imporcie, Google Chrome oraz Chromium w wersji 61 wyświetlają ostrzeżenie, że zostaną usunięte w wersji M65, aby rozwiązać ten problem możemy dołączyć style do głównego dokumentu.

{% highlight html %}
<script>
(function() {
    var url = 'https://cdn.jsdelivr.net/npm/jquery.terminal@1.7.0/css/jquery.terminal.min.css';
    var $ = jQuery.noConflict();
    $('<link href="' + url + '" rel="stylesheet"/>').appendTo('head');
    var importDoc = document.currentScript.ownerDocument;
    var style = importDoc.querySelector('style');
    document.head.appendChild(style);
    window.terminal = function(selector) {
        var args = [].slice.call(arguments, 1);
        return $.fn.terminal.apply($(selector), args);
    };
})();
</script>
{% endhighlight %}

**NOTE:** Podany plik można znaleźć w [repo biblioteki](https://raw.githubusercontent.com/jcubic/jquery.terminal/master/import.html).


## Dostęp do zawartości

Za pomocą importów mamy także dostęp do zawartości danych plików, tak jak w przypadku AJAX-a, za pomocą właściwości import elementu link:

{% highlight html %}
<link rel="import" href="https://cdn.jsdelivr.net/npm/jquery.terminal@1.7.0/js/jquery.terminal.min.js"
      onload="console.log(event.target.import.body.innerHTML)"/>
{% endhighlight %}

Tak jak w przypadku obrazków mamy dostęp do zdarzeń `onload` i `onerror`.

## CORS

Tak jak w przypadku AJAX-a, a w odróżnieniu od stylów CSS, aby użyć importu strona musi mieć włączony CORS, przeciwnym wypadku nie tylko nie będziemy mieli dostępu do zawartości ale import nie załaduje się wcale,

## Web komponenty

Dzięki importom HTML możemy w łatwy sposób dołączać web komponenty

{% highlight html %}
<script>
  // Definicja komponentu <czesc>.
  var proto = Object.create(HTMLElement.prototype);

  proto.createdCallback = function() {
    this.innerHTML = 'Cześć, <b>' +
                     (this.getAttribute('name') || '?') + '</b>';
  };

  document.registerElement('czesc', {prototype: proto});
</script>
{% endhighlight %}

Dołączając taki import mamy możliwość skorzystania z nowego taga `czesc`

{% highlight html %}
<head>
<link ref="import" href="komponent.html"/>
</head>
<body>
  <czesc name="Jan"><czesc>
</body>
{% endhighlight %}

## Wsparcie przeglądarek

Wszystko Fajnie, ale jakie jest wsparcie dla importów HTML w przeglądarkach? Niestety nie jest dobrze, z chwilą pisania tego artykułu tylko Chrome we wszystkich wersjach oraz Opera mają tą funkcjonalność. Wsparcie można zobaczyć na stronie [Can I Use](http://caniuse.com/#feat=imports). Miejmy nadzieje, że wkrótce się to zmieni i wszystkie przeglądarki zaimplementują importy HTML, do tego czasu można użyć [polyfill](https://github.com/polymer/HTMLImports).

*[HTML]: Hypertext Markup Languagexs
*[CSS]: Cascading Style Sheets
*[AJAX]: Asynchronous JavaScript And XML
*[CORS]: Cross-Origin Resource Sharing
*[Scope]: ang. Zasięg Zmiennych
