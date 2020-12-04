---
layout: post
title:  "Jak napisać prostą bibliotekę JavaScript do obsługi DOM"
date:   2018-11-20 22:13:31+0100
categories:
tags: javascript dom jquery biblioteki
author: jcubic
description: Mimo że biblioteka jQuery jest nadal polularna przeglądarki www udsotąpniają miej więcej takie same API. Napisanie własnej biblioteki JavaScript do obłsugi DOM, nie jest wcale takie trudne.
image:
  url: "/img/document-object-model.png"
  alt: "Ikona przedstawiajaca strukturę drzewa wewnątrz okręu oraz słowo DOM"
  width: 800
  height: 500
  attribution: "Oryginał [WHATWG](https://whatwg.org/), [źródło](https://resources.whatwg.org/) licencja [CC-BY 4.0](https://creativecommons.org/licenses/by/4.0/)"
related:
  -
    name: "O czym pamiętać tworząc bibliotekę Open Source w JavaScript"
    url: "/2018/03/o-czym-pamietac-tworzac-biblioteke-open-source.html"
---

Główną siłą biblioteki jQuery, było to, że poprawiała błędy przeglądarek i różnice w ich
API. Ale to już w większości przypadków nie jest potrzebne, ponieważ nowoczesne
przeglądarki udostępniają prawie taki samo API. W innych przypadkach jQuery nie poprawia
wszystkich błędów, które znajdują się w implementacjach DOM i tak trzeba pisać kod, który
łata te bugi. Inną super cechą jQuery jest prostota API, którym można się inspirować, aby
pisać własne biblioteki JavaScript.


W tym wpisie przedstawię, jak można zacząć pisać prostą bibliotekę JavaScript do obsługi DOM,
która może zastąpić jQuery. Oczywiście tylko wtedy, gdy chcemy pisać aplikację w czystym JS
(tzw. Vanilla), a nie w jakimś framework-u architektonicznym jak React, Angular czy Vue.

<!-- more -->

## Konstruktor

Przejdźmy od razu do kodu naszej biblioteki JavaScript.

Pierwsza rzecz to konstruktor, który będzie znajdował i opakowywał elementy na stronie.

{% highlight javascript %}
function DOM(arg) {
    if (!(this instanceof DOM)) { // 1
        return new DOM(arg);
    }
    if (arg instanceof Array) { // 2
        this._nodes = arg;
    } else if (typeof arg === 'string') { // 3
        return this.find(arg);
    } else if (arg instanceof Element) { // 4
        this._nodes = [arg];
    } else {
        this._nodes = [];
    }
}
{% endhighlight %}

W (1) sprawdzamy czy DOM został wywołany jako konstruktor czy jako funkcja, upraszcza to
kod ponieważ nie trzeba pisać new, a zawsze dostaniemy nowy obiekt. W (2) sprawdzamy czy
przekazano tablicę, zakładamy w takim przypadku, że jest to lista elementów DOM.  Jeśli
argumentem jest ciąg znaków (3) wywołujemy funkcje `find`, która znajdzie element w
drzewie DOM (napiszemy ja za chwilę).  Funkcja obsługuje też przypadek, gdy przekażemy do
niej obiekt DOM Node (4).

## Prototyp

Następnie zdefiniujemy prototyp naszego konstruktora, jeśli wolisz możesz zastąpić ten kod
klasą ES6, ale ja użyje prototypu.

{% highlight javascript %}
DOM.fn = DOM.prototype = {
    find: function(selector) {
        if (this._nodes && this._nodes.length) { // 1
            var nodes = [];
            this._nodes.forEach(function(node) {
                nodes = nodes.concat([].slice.call(node.querySelectorAll(selector))); // 2
            });
            return new DOM(nodes);
        }
        return new DOM([].slice.call(document.querySelectorAll(selector))); // 3
    }
};
{% endhighlight %}

Użyłem przypisania `DOM.fn`, tak jak to jest w jQuery, aby umożliwić prostsze dodawanie
pluginów. W jQuery są to po prostu funkcje, dodawane do prototypu.


W funkcji `find` sprawdzane jest, czy istnieją elementy node, ponieważ można ją wywołać z
konstruktora, zanim zostanie przypisana do nich wartość.  Jeśli istnieją to dla każdej
elementu wyszukiwany jest nowy element i dodawany do listy (2). `querySelectorAll` zwraca
obiekt typu `NodeList`, a my potrzebujemy tablicy, dlatego używamy tricku z
`[].slice.call`. Można go zastąpić:

{% highlight javascript %}
nodes = nodes.concat(...node.querySelectorAll(selector));
{% endhighlight %}

ale wtedy kod nie będzie działał w IE i będziemy musieli użyć narzędzia Babel, w celu
konwersji do ES5. Można także użyć funkcji `Array.from`.

Jeśli nie ma żadnych elementów czyli używamy np. `DOM('body ul');`, to wywoływany jest
`querySelectorAll` na obiekcie `document` (3).

## Obsługa Zdarzeń

Przydała by nam się obsługa zdarzeń (ang. events). Oto prosta funkcja:

{% highlight javascript %}
DOM.fn = DOM.prototype = {
    find: function(selector) { /* kod funkcji */ },
    on: function(event_name, fn) {
        this._nodes.forEach(function(node) {
            node.addEventListener(event_name, fn);
        });
        return this;
    }
};
{% endhighlight %}

W funkcji zwracamy `this` i tak będzie także z innymi funkcjami. Dzięki temu będziemy
mogli łączyć wywołania funkcji w łańcuchy, tak jak to jest realizowane w przypadku
biblioteki jQuery.

Jak mamy dodawanie zdarzeń, to musimy dodać ich usuwanie:

{% highlight javascript %}
DOM.fn = DOM.prototype = {
    find: function(selector) { /* kod funkcji */ },
    on: function(event_name, fn) { /* kod funkcji */ },
    off: function(event_name, fn) {
        this._nodes.forEach(function(node) {
            node.removeEventListener(event_name, fn);
        });
        return this;
    }
};
{% endhighlight %}

Funkcje `addEventListener` oraz `removeEventListener` są dostępne w każdej przeglądarce,
nawet IE9, więc nie trzeba pisać już kodu z `attachEvent` dla IE, szczegóły na
[MDN](https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener).

## Dodawanie nowych elementów do drzewa DOM

Teraz fajnie by było móc dodawać nowe elementy do HTML.

{% highlight javascript %}
DOM.fn = DOM.prototype = {
    find: function(selector) { /* kod funkcji */ },
    on: function(event_name, fn) { /* kod funkcji */ },
    off: function(event_name, fn) { /* kod funkcji */ },
    create: function(arg) {
        if (typeof arg === 'string') {
            if (arg.match(/^\s*<.*>\s*$/)) { // 1
                var temp = document.createElement('template');
                temp.innerHTML = arg; // 2
                var nodes = temp.content ? temp.content.children : temp.childNodes; // 3
                return new DOM([].slice.call(nodes)); // 4
            } else {
                var element = document.createElement(arg); // 5
                return new DOM([element]);
            }
        }
    }
};
{% endhighlight %}

Najpierw sprawdzamy czy argument wygląda jak html za pomocą wyrażenia regularnego (1).  W
przypadku gdy argument to html, używamy `innerHTML`, dzięki temu przeglądarka skonwertuje
za nas, dowolny html do elementów DOM (1). `temp.content` jest to specjalny obiekt o
nazwie document fragment. W przeglądarce IE nie jest on jednak dostępny, dlatego
sprawdzamy czy istnieje (2). Taki kod `temp.childNodes || temp.content.children` nie
zadziała, ponieważ element posiada to pole z obiektu `Node`, po którym dziedziczy
(jest w łańcuchu prototypów). Mając utworzone elementy, możemy je skonwertować na tablicę
(3). Jeśli wartością nie jest html, to zakładamy, że to jest nazwa taga html, dlatego
tworzymy nowy element za pomocą funkcji `createElement` (3). W obu przypadkach funkcja
zwraca nową instancje obiektu DOM.

## Tworzenie elementów DOM

Jedyny problem z tym rozwiązaniem to tworzenie html `<td>foo</td>`, w przeglądarce IE, bez
rodzica `table`.  W tej przeglądarce zostanie utworzony sam tekst bez elementu, tak jest z
każdym elementem który powinien być w tabeli.  Aby to rozwiązać biblioteka jQuery tworzy
wrapper czyli `<table><tbody><tr>` dla `td`. Rozwiązanie tego problemu wygląda tak:

{% highlight javascript %}
DOM.fn = DOM.prototype = {
    find: function(selector) { /* kod funkcji */ },
    on: function(event_name, fn) { /* kod funkcji */ },
    off: function(event_name, fn) { /* kod funkcji */ },
    create: function(arg) {
        if (typeof arg === 'string') {
            var elements;
            if (arg.match(/^\s*<.*>\s*$/)) {
                var temp = document.createElement('template');
                if (temp.content == void 0) { // ie11
                    if (/^[^\S]*?<(t(?:head|body|foot|r|d|h))/i.test(arg)) {
                        temp.innerHTML = '<table>'+arg;
                        elements = temp.querySelector(RegExp.$1).parentNode.childNodes;
                    } else {
                        temp.innerHTML = arg;
                        elements = temp.childNodes;
                    }
                } else {
                    temp.innerHTML = arg;
                    elements = temp.content.children;
                }
                elements = [].slice.call(elements);
            } else {
                elements = [document.createElement(arg)];
            }
            return new DOM(elements);
        }
    }
};
{% endhighlight %}

Rozwiązanie bazuje na odpowiedzi ze
[StackOverflow](https://stackoverflow.com/a/43293361/387194).

Mając taką funkcje `create` można sprawdzać w konstruktorze, czy ciąg znaków wygląda jak
html i oddelegowywać robotę do `create`, tak jak to robi jQuery.

{% highlight javascript %}
function DOM(arg) {
    if (!(this instanceof DOM)) {
        return new DOM(arg);
    }
    if (arg instanceof Array) {
        this._nodes = arg;
    } else if (typeof arg === 'string') {
        if (arg.match(/^\s*<.*>\s*$/)) {
            return this.create(arg);
        }
        return this.find(arg);
    } else if (arg instanceof Element) {
        this._nodes = [arg];
    } else {
        this._nodes = [];
    }
}
{% endhighlight %}

## Metody statyczne

Funkcje find oraz create zwracają nowy obiekt DOM i można je używać bez obiektu,
zakładając że nie używamy strict mode lub gdy nie mamy w obiekcie `window` zmiennej
`_nodes`. Dlatego można je dodać do funkcji DOM, będą mogły być używane, jak metody
statyczne.

{% highlight javascript %}
['find', 'create'].forEach(function(fn) {
  DOM[fn] = DOM.fn[fn];
});
{% endhighlight %}

Jeśli boli cię, że funkcja find zawiera odwołanie do `this`, które nie zadziała w strict
mode, to możesz poprawić ją w sposób, w jaki utworzyliśmy konstruktor biblioteki.

{% highlight javascript %}
DOM.fn = DOM.prototype = {
  find: function(selector) {
    // dodane this instanceof DOM
    if (this instanceof DOM && this._nodes && this._nodes.length) { // 1
      var nodes = [];
      this._nodes.forEach(function(node) {
        nodes = nodes.concat([].slice.call(node.querySelectorAll(selector))); // 2
      });
      return new DOM(nodes);
    }
    return new DOM([].slice.call(document.querySelectorAll(selector))); // 3
  },
  on: function(event_name, fn) { /* kod funkcji */ },
  off: function(event_name, fn) { /* kod funkcji */ },
  create: function(arg) { /* kod funkcji */ }
};
{% endhighlight %}

## Funkcja html

Kiedy mamy funkcje `create` i chcemy utworzyć tag html za pomocą nazwy taga, to może nam się
przydać funkcja `html`.

{% highlight javascript %}
DOM.fn = DOM.prototype = {
    find: function(selector) { /* kod funkcji */ },
    on: function(event_name, fn) { /* kod funkcji */ },
    off: function(event_name, fn) { /* kod funkcji */ },
    create: function(arg) { /* kod funkcji */ },
    html: function(html) {
        if (typeof html === 'undefined') {
            return this._nodes[0].innerHTML;
        }
        this._nodes.forEach(function(node) {
            node.innerHTML = html;
        });
        return this;
    }
};
{% endhighlight %}

Teraz możemy użyć np.:

{% highlight javascript %}
DOM.create('li').html('Hello')
{% endhighlight %}

## Dodawanie do drzewa

Następnym krokiem, jest dodanie utworzonego elementu do drzewa, a oto dwie funkcje znane z
jQuery:

{% highlight javascript %}
DOM.fn = DOM.prototype = {
  find: function(selector) { /* kod funkcji */ },
  on: function(event_name, fn) { /* kod funkcji */ },
  off: function(event_name, fn) { /* kod funkcji */ },
  create: function(arg) { /* kod funkcji */ },
  html: function(html) { /* kod funkcji */ },
  append: function(arg) {
    if (arg instanceof DOM) {
      this._nodes.forEach(function(node) {
        arg._nodes.forEach(function(arg) {
          node.appendChild(arg);
        });
      });
    }
    return this;
  },
  appendTo: function(arg) {
    DOM(arg).append(this);
    return this;
  }
};
{% endhighlight %}

Teraz możemy użyć:

{% highlight javascript %}
DOM('body').find('ul').on('click', function(e) {
  var color = e.target.style.color == 'red' ? 'black' : 'red';
  e.target.style.color = color;
}).append(DOM.create('li').html('Hello'));
{% endhighlight %}

## Delegacja zdarzeń

Nasz mechanizm zdarzeń, niestety nie działa dla nowych elementów, ponieważ zdarzenia są
dodawane bezpośrednio do elementu.  Aby rozwiązać ten problem, musimy zmodyfikować naszą
obsługę zdarzeń, aby uzyskać ich delegacje.

{% highlight javascript %}
DOM.fn = DOM.prototype = {
  find: function(selector) { /* kod funkcji */ },
  on: function(event_name, fn) {
    if (typeof arguments[1] === 'string') {
      var selector = arguments[1]; // 1
      fn = arguments[2];
    }

    this._nodes.forEach(function(node) {
      if (selector) {
        if (!node._delegate) {
          node._delegate = {
            handler: function(event) { // 2
              node._delegate.callbacks.forEach(function(callback) {
                // msMatchesSelector dostępne od IE9
                var element = event.target;
                var matches = (element.msMatchesSelector || element.matches).bind(element);
                if (matches(callback.selector)) { // 3
                  callback.fn.call(null, event);
                }
              });
            },
            callbacks: [{fn: fn, selector: selector}] // 4
          };
          node.addEventListener(event_name, node._delegate.handler); // 5
        } else {
          node._delegate.callback.push({fn: fn, selector: selector}); // 6
        }
      } else {
        node.addEventListener(event_name, fn); // 7
      }
    });
    return this;
  },
  off: function(event_name, fn) { /* kod funkcji */ },
  create: function(arg) { /* kod funkcji */ },
  html: function(html) { /* kod funkcji */ },
  append: function(arg) { /* kod funkcji */ },
  appendTo: function(arg) { /* kod funkcji */ },
};
{% endhighlight %}

W funkcji mamy podobny mechanizm jak w jQuery, gdy drugi argument to ciąg znaków,
zakładamy, że jest to selektor CSS (1). Gdy mamy selektor dla każdego elementu w tablicy
`_nodes` tworzymy nowe pole `_delegate`, które zawiera `handler`, czyli naszą funkcję
obsługi zdarzenia (2), którą dodajemy tylko raz (5). Do pola `_delegate`, dodajemy także
tablicę obiektów callbacks (4), która zawiera selektor oraz funkcję. Wywołujemy ją gdy
selektor pasuje do elementu, dla którego odpaliło się zdarzenie (3). Jeśli `_delegate`
jest zdefiniowane, czyli mamy już podpiętego obserwatora zdarzenia, możemy po prostu dodać
nową funkcję do tablicy callbacks, wraz z selektorem (6). Jeśli nie ma selektora tzn. że
to zwykły przypadek zdarzenia do elementu (7).

Musimy także zmodyfikować naszą funkcje do usuwania zdarzeń.

{% highlight javascript %}
DOM.fn = DOM.prototype = {
  find: function(selector) { /* kod funkcji */ },
  on: function(event_name, fn) { /* kod funkcji */ },
  off: function(event_name, fn) {
    if (typeof arguments[1] === 'string') {
      var selector = arguments[1];
      fn = arguments[2];
    }
    this._nodes.forEach(function(node) {
      if (selector && node._delegate) {
        if (fn) {
          node._delegate.callbacks = node._delegate.filter(function(callback) {
            return callback.fn !== fn; // 1
          });
        } else {
          node._delegate.callbacks = node._delegate.filter(function(callback) {
            return callback.selector !== selector; // 2
          });
        }
        if (!node._delegate.callbacks.length) {
          node.removeEventListener(event_name, node._delegate.handler); // 3
          delete node._delegate;
        }
      } else {
        node.removeEventListener(event_name, fn); // 4
      }
    });
    return this;
  },
  create: function(arg) { /* kod funkcji */ },
  html: function(html) { /* kod funkcji */ },
  append: function(arg) { /* kod funkcji */ },
  appendTo: function(arg) { /* kod funkcji */ },
};
{% endhighlight %}

Usuwając zdarzenie musimy sprawdzić, czy ustawiono selektor, funkcje oraz pole
`_delegate`. Jeśli ten warunek jest spełniony, usuwamy obiekt z tablicy `callbacks`, za
pomocą metody `filter` dla tablic, sprawdzając czy nie jest to funkcja, którą chcemy usunąć
(1). Jeśli nie mamy funkcji sprawdzamy selektor (2). Jeśli nie ma już nic w tablicy
`callbacks`, usuwamy zdarzenie oraz zmienną (3). W przypadku gdy nie mamy selektora tzn. że
mamy zwykłe zdarzenie, więc usuwamy zdarzenie w zwykły sposób (4).

Nasze użycie biblioteki, czyli zdarzenie `click`, będzie działać z nowymi elementami:

{% highlight javascript %}
DOM('body').find('ul').on('click', function(e) {
  var color = e.target.style.color == 'red' ? 'black' : 'red';
  e.target.style.color = color;
}).append(DOM.create('li').html('Hello'));
{% endhighlight %}

## Obsługa stylów

Użycie style trochę źle wygląda, napiszmy funkcje do zmieniania css.

{% highlight javascript %}
DOM.fn = DOM.prototype = {
  find: function(selector) { /* kod funkcji */ },
  on: function(event_name, fn) { /* kod funkcji */ },
  off: function(event_name, fn) { /* kod funkcji */ },
  create: function(arg) { /* kod funkcji */ },
  html: function(html) { /* kod funkcji */ },
  append: function(arg) { /* kod funkcji */ },
  appendTo: function(arg) { /* kod funkcji */ },
  css: function(arg, value) {
    var self = this;
    if (typeof arg === 'string') {
      if (typeof value !== 'undefined') {
        this._nodes.forEach(function(node) {
          node.style[arg] = value;
        });
      } else if (this._nodes.length) {
        return this._nodes[0].style[arg];
      }
    } else if (typeof arg === 'object' && arg !== null) {
      Object.keys(arg).forEach(function(key) {
        self.css(key, arg[key]);
      });
    } else
    return this;
  }
};
{% endhighlight %}

Funkcja używa tylko właściwości style, jeśli chcemy aby pobierała też wartości z arkuszy
stylów, należy użyć funkcji
[getComputedStyle](https://developer.mozilla.org/en-US/docs/Web/API/Window/getComputedStyle).

Funkcja nie obsługuje też zmiennych css (ang. Custom Properties), aby je pobierać i
zapisywać najlepiej użyć:

{% highlight javascript %}
if (typeof value !== 'undefined') {
  this._nodes.forEach(function(node) {
    node.style.setProperty(arg, value);
  });
} else if (this._nodes.length) {
  var style = getComputedStyle(this._nodes[0]);
  return style.getPropertyValue(arg);
}
{% endhighlight %}

Będzie to uniwersalne i działało z normalnymi właściwościami jak i ze zmiennymi css. (dla
porównania funkcja `css()` w bibliotece jQuery nie działała, ze zmiennymi CSS
aż do wersji bodajże 3.4). To rozwiązanie spowoduje inny problem, a mianowicie kolory,
będą konwertowane do znormalizowanej postaci (może być problematyczne porównywanie z przypisaną
wartością jak w naszym przykładzie). W przypadku Google Chrome będzie to ciąg znaków
`rgb(255, 0, 0)` dla koloru czerwonego. Rozwiązanie tego problemu zostawiam czytelnikowi.

Warto też obsłużyć mapowanie stylów, aby można było pobierać i przypisywać
np. 'padding-left', który ma swój odpowiednik w style jako `paddingLeft`. Aby zrobić
poprawkę dla funkcji `css`, warto sprawdzić jak jQuery to obsługuje, a używa modułu
[core/camelCase](https://github.com/jquery/jquery/blob/354f6036f251a3ce9b24cd7b228b4c7a79001520/src/core/camelCase.js).

Drugą rzecz, jaką można poprawić, to sprawdzać, czy jest przypisywana liczba. Wtedy trzeba
zamienić ją na ciąg znaków i dodać `px` na końcu (`node.style.paddingLeft = 10;` nie
zadziała). W bibliotece jQuery, są też wyjątki takie jak np. `zIndex`. Lista znajduje się
kodzie źródłowym jQuery
[w pliku css.js linia 190](https://github.com/jquery/jquery/blob/354f6036f251a3ce9b24cd7b228b4c7a79001520/src/css.js#L190).

## Pluginy

Teraz czas na prosty plugin:

{% highlight javascript %}
DOM.fn.color = function(arg) {
  return this.css('color', arg);
};
{% endhighlight %}

dzięki temu pluginowi możemy zmienić nasz kod obsługi `click`:

{% highlight javascript %}
DOM('body').find('ul').on('click', 'li', function(e) {
  var self = DOM(e.target);
  self.color(self.color() == 'red' ? 'black' : 'red');
});
{% endhighlight %}

Właściwie już z funkcją `css`, mogliśmy mieć praktycznie to samo, ale to jest tylko
przykład użycia pluginu.

## Usuwanie elementów z drzewa DOM

Ostatnia funkcja, jaka została do dodania, to `remove`:

{% highlight javascript %}
DOM.fn = DOM.prototype = {
  find: function(selector) { /* kod funkcji */ },
  on: function(event_name, fn) { /* kod funkcji */ },
  off: function(event_name, fn) { /* kod funkcji */ },
  create: function(arg) { /* kod funkcji */ },
  html: function(html) { /* kod funkcji */ },
  append: function(arg) { /* kod funkcji */ },
  appendTo: function(arg) { /* kod funkcji */ },
  css: function(arg, value) { /* kod funkcji */ },
  remove: function() {
    this._nodes.forEach(function(node) {
      node.parentNode.removeChild(node);
    });
    return this;
  }
};
{% endhighlight %}

Zamiast usuwania za pomocą `removeChild` dla rodzica, można użyć
[ChildNode.remove()](https://developer.mozilla.org/en-US/docs/Web/API/ChildNode/remove).
Niestety funkcja jest niedostępna w IE, dlatego użyłem `removeChild`.

Można teraz wywołać taki kod:

{% highlight javascript %}
DOM('body').find('ul').on('click', 'li', function(e) {
  var self = DOM(e.target);
  self.color(self.color() == 'red' ? 'black' : 'red');
}).append(DOM.create('li').html('Hello')).find('li:nth-child(1)').remove();

DOM('<div><p>Hello</p></div><div><p>Hello</p></div>').appendTo('body').css('color', 'red');
{% endhighlight %}

Jeśli mamy taki html:

{% highlight html %}
<body>
  <ul>
    <li>Foo</li>
    <li>Bar</li>
  </ul>
</body>
{% endhighlight %}

To po odpaleniu tego kodu, będziemy mieli taką strukturę (a dokładnie jej odpowiednik w
drzewie DOM):

{% highlight html %}
<body>
  <ul>
    <li>Bar</li>
    <li>Hello</li>
  </ul>
  <div style="color: red;">
    <p>Hello</p>
  </div>
  <div style="color: red;">
    <p>Hello</p>
  </div>
</body>
{% endhighlight %}

Jeśli klikniemy na którymś z elementów li, dostanie on atrybut `style="color: red"`, jeśli
nie użyto funkcji `getComputedStyle`, lub `style="color: rgb(255,0,0)"` w przypadku gdy
jej użyto.

## Podsumowanie

Można powiedzieć, że podstawa biblioteki jest gotowa, ale na pewno brakuje wielu
funkcji. Najlepiej użyć tej biblioteki w jakimś projekcie i dodawać nowe funkcje jak będą
potrzebne. Ale nie powinno się przesadzać z dodawaniem nowych ficzerów do
biblioteki. Najlepsze są takie, które mają proste API. Można dodać nowy plik z pluginami i
jak jakiś będzie używany więcej niż 4 razy, to dodać go do biblioteki. Tak postępowali z
jQuery. Można się także sugerować funkcjami, które są w jQuery, których nie jest tak
dużo. Niektórych API z jQuery nie potrzebujmy, np. ajax można zastąpić funkcją `fetch`,
która jest dostępna w [87% przeglądarek](https://caniuse.com/#feat=fetch), a gdy
potrzebujemy 100% można użyć polyfill np.
[bibliteka unfetch](https://github.com/developit/unfetch), której autorem jest Jason
Miller autor biblioteki Preact, uproszczonej wersji React.js.
