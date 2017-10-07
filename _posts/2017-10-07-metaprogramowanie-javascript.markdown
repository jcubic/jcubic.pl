---
layout: post
title:  "Metaprogramowanie w języku JavaScript"
date:   2017-10-07 17:47:12+0200
categories:
tags:  javascript es6
author: jcubic
description: Wraz z wersją języka JavaScript ES6 dostaliśmy potężne narzędzia umożliwiające metaprogramowanie, a dokładnie podpinanie się pod wbudowane mechanizmy języka.
---

Wraz z wersją języka JavaScript ES6 dostaliśmy potężne narzędzia umożliwiające metaprogramowanie, a dokładnie podpinanie się pod wbudowane mechanizmy języka. W tym poście przedstawie jakie nowe funkcje weszły do ES6, które umożliwiają metaprogramowanie.

<!-- more -->

Do ES6 weszły dwa mechanizmy obiekt Proxy oraz symbole. Poniżej opis obu tych mechanizmów:

## Symbole

Symbole to nowy typ danych wbudowanych, aby utworzyć symbol stosujemy:

{% highlight javascript %}
Symbol("nazwa")
{% endhighlight %}

to wywołanie za każdym razem utworzy nowy symbol, nawet jeśli użyjemy tej samej nazwy, tzn.:

{% highlight javascript %}
Symbol('foo') == Symbol('foo')
{% endhighlight %}

zwróci wartość `false`, czyli działa to tak jak funkcja `gensym` z języka lisp. Jeśli jednak chcemy pobrać ten sam symbol możemy skorzystać z funkcji `Symbol.for`:

{% highlight javascript %}
Symbol.for('foo') == Symbol.for('foo')
{% endhighlight %}

powyższy kod z kolei zwróci wartość `true`.

Można ich np. używać jako stałych do przechowywania wartości np. jeśli potrzebujemy utworzyć stałe, które określają typ:

{% highlight javascript %}
library.NAME = 1;
library.LAST_NAME = 2;

function set(type, value) {
   if (type == library.NAME) {
       object.name = value;
   } else if (type == library.LAST_NAME) {
       object.last = value;
   }
}

set(library.NAME, 'Jan');
set(library.LAST_NAME, 'Kowalski');
{% endhighlight %}

możemy zamiast liczb lub stringów skorzystać z symboli:

{% highlight javascript %}
library.NAME = Symbol('name');
library.LAST_NAME = Symbol('last_name');
{% endhighlight %}

Ciekawsze są jednak wbudowane Symbole, które można dodawać jako wartości obiektów. Działają one jak magiczne metody z Pythona lub PHP. Oto one:

### Symbole do wyrażeń regularnych

Dają one możliwość używania dowolnego obiektu jak wyrażenia regularnego, czyli jako argument do metod obiektu string: `split`, `match`, `search`, `replace`

Poniżej przykładowy kod obiektu, który działa dla każdej z tych funkcji:

{% highlight javascript %}
function RepeatMatcher(chr, strict) {
  var re;
  if (strict) {
    re = new RegExp('^(?:' + chr + ')+$');
  } else {
    re = new RegExp('(?:' + chr + ')+');
  }
  this[Symbol.replace] = function(string, replacement) {
    return string.replace(re, replacement);
  };
  this[Symbol.match] = function(str) {
    return str.match(re);
  };
  this[Symbol.search] = function(string) {
    return string.search(re);
  };
  this[Symbol.split] = function(string) {
    return string.split(re);
  };
}
{% endhighlight %}

Jest w tym kodzie trochę powtórzeń, na końcu artykułu będzie link do kodu na Codepen, gdzie użyłem funkcji wyższego poziomu (ang. Higher Order Function) aby utworzyć wszystkie 4 funkcje w pętli.

Aby użyć danej funkcji tworzymy nowy obiekt tej "klasy" (osobiście nie przepadam za nowym tworzeniem klas za pomocą słowa kluczowego `class` i nadal tworze zwykłe funkcje) np:

{% highlight javascript %}
var m = new RepeatMatcher('s', true);
console.log('ssss'.match(m));
console.log('sass'.match(m));
console.log('__www__'.replace(new RepeatMatcher('w'), (_) => _ + '_x'));
console.log('foo____bar___baz'.split(new RepeatMatcher('_')));
{% endhighlight %}

wynikiem będzie:

```
["ssss"]
null
"__www_x__"
["foo","bar","baz"]
```

### Symbol iteracji

`Symbol.iterator` to symbol, który daje nam możliwość podpięcia się pod pętle `for..of` (to nowa pętla służąca do iterowania po obiektach takich jak Array, Map, Set, String, TypedArray, arguments). Poniżej krótki kod, który dodaje możliwość iterowania po obiekcie Todos:

{% highlight javascript %}
function Todos(init) {
  var data = init != undefined ? (init instanceof Array ? init : [init] ) : [];
  this.append = function(item) {
    data.push(item);
  };
  this[Symbol.iterator] = function*() {
    for (var i=0; i<data.length; ++i) {
      yield data[i];
    }
  };
}
{% endhighlight %}

w tej "klasie" użyto funkcji generatora (gwiazdka) oraz słowa kluczowego yield nowe w ES6. Mając instancje tej funkcji/klasy można użyć pętli `for..of` aby iterować po wartościach todo:

{% highlight javascript %}
var todos = new Todos(['one']);
todos.append('hey');
todos.append('Jo');
for (let todo of todos) {
  console.log(todo);
}
{% endhighlight %}

### Symbol.species

To symbol, który służy do podpięcia się pod tworzenie nowego obiektu tego samego typu np. w funkcji map:

{% highlight javascript %}
class Lorem extends Array {
    static get [Symbol.species]() {
        return this;
    }
}

class Ipsum extends Array {
    static get [Symbol.species]() {
        return Array;
    }
}
{% endhighlight %}

Tutaj skorzystałem z klas bo chyba nie da się za pomocą zwykłych funkcji stworzyć klasę pochodną po obiekcie `Array`.

{% highlight javascript %}
const noop = () => {};
console.log('instanceof Foo', new Lorem().map(noop) instanceof Lorem);
console.log('instanceof Bar', new Ipsum().map(noop) instanceof Ipsum);
console.log('instanceof Array', new Ipsum().map(noop) instanceof Array);
{% endhighlight %}

Gdyby nie było `Symbol.species` tylko ostatnia wartość byłaby prawdziwa.

### Konwersja typów

Mamy możliwość podpięcia się pod automatyczne konwertowanie typów JavaScript. Służy do tego symbol `Symbol.toPrimitive`:


{% highlight javascript %}
function Answer() {
  this[Symbol.toPrimitive] = function(hint) {
    log('convert to ' + hint);
    if (hint == 'string') {
      return 'the answer is 42';
    } else if (hint == 'number') {
      return 42;
    } else {
      return 42;
    }
  }
}

var answer = new Answer();
console.log(+answer);
console.log('' + answer);
console.log(String(answer));
{% endhighlight %}

Niestety dodanie do liczby albo do łańcucha znaków nie przekazuje wartości `'number'` i `'string'` tylko wartość `'default'` przynajmniej w przeglądarkach Chromium i Chrome.

## Proxy

Drugim elementem języka, dającym możliwości metaprogramowania, są obiekty typu proxy. Są to obiekty, które są pośrednikami dla jakieś innego obiektu. Obiekt proxy posiada tzw. pułapki (ang. trap), które dają możliwość podpięcia się pod pobieranie wartości obiektu, przypisanie wartości, usuwanie wartości, użycie operatora new (możemy używać obiektu proxy jako konstruktora/klasy) oraz wywołania jak funkcji (ten ostatni wymaga aby obiektem, dla którego tworzony jest proxy, była funkcja - może być pusta). Poniżej funkcja, która tworzy nowy obiekt proxy dla każdego wymienionych pułapek:

{% highlight javascript %}
function proxify() {
  var object = Object.create(arguments[0]);
  var props = [];
  var proxy = new Proxy(function() {}, {
    get: function(target, name) {
      console.log('get', name);
      return object[name];
    },
    set: function(target, name, value) {
      console.log('set', name, value)
      props.push(name);
      object[name] = value;
    },
    construct: function() {
      console.log('construct');
      return proxify(object);
    },
    apply: function(target, thisArg, args) {
      return args.reduce(function(a, b) {
        return a + b;
      }, 0);
    },
    deleteProperty: function(target, name) {
      console.log('delete', name);
      if (props.indexOf(name) != -1) {
        delete object[name];
      }
    }
  });
  return proxy;
}
{% endhighlight %}


zauważ że nie przekazujemy oryginalnego obiektu do konstruktora proxy tylko korzystamy z domknięcia, zazwyczaj korzysta się z obiektu proxy w ten sposób:

{% highlight javascript %}
var foo = new Proxy(obiekt, {
   get: function(target, name) {
       if (name == 'bar') {
           return target[name];
       }
   }
});
{% endhighlight %}

Funkcji proxify można użyć w ten sposób:


{% highlight javascript %}

var Klasa = proxify({foo: '10'});
console.log(Klasa.foo);

var obiekt = new Klasa();
console.log(obiekt.foo);
foo.bar = 20;
console.log(obiekt.bar);

delete obiekt.bar;
console.log(obiekt.bar);
delete obiekt.foo;
console.log(obiekt.foo);

console.log(obiekt(1,2,3));
{% endhighlight %}

Dzięki temu że pułapka construct zwraca nowy obiekt proxy można używać takich dziwnych konstrukcji:

{% highlight javascript %}
var obiekt = new (new (new (new (new Klasa()))));
{% endhighlight %}


Jeśli zastanawiasz się czy można łączyć obiekt proxy z symbolami, odpowiedź brzmi tak. Aby dodać metodę symbolu do obiektu Proxy, trzeba skorzystać z pułapki `get` i sprawdzać czy jest to odpowiedni symbol. Jeśli tak to zwracać odpowiednią funkcje np. poniżej nasza poprzednia funkcja z dodanym symbolem `Symbol.toPrimitive`:

{% highlight javascript %}
function proxify() {
  var object = Object.create(arguments[0]);
  function convert(hint) {
    log('convert', hint);
    if (hint == 'number') {
      return object.foo;
    } else if (hint == 'string') {
      return JSON.stringify(object);
    }
  };
  var props = [];
  var proxy = new Proxy(function() {}, {
    get: function(target, name) {
      log('get', name);
      if (name == Symbol.toPrimitive) {
        return convert;
      }
      return object[name];
    },
    set: function(target, name, value) {
      log('set', name, value)
      props.push(name);
      object[name] = value;
    },
    construct: function() {
      log('construct');
      return proxify(object);
    },
    apply: function(target, thisArg, args) {
      return args.reduce(function(a, b) {
        return a + b;
      }, 0);
    },
    deleteProperty: function(target, name) {
      log('delete', name);
      if (props.indexOf(name) != -1) {
        delete object[name];
      }
    }
  });
  return proxy;
}
{% endhighlight %}

Możesz zobaczyć wsparcie dla zdefiniowanych symboli na stronie [kangax.github.io/compat-table/es6](http://kangax.github.io/compat-table/es6/#test-well-known_symbols). Wszystkie nowoczesne przeglądarki przeszły testy oprócz IE oraz Edge.

Wsparcie dla obiektu Proxy możesz zobaczyć na [Can I Use](http://caniuse.com/#feat=proxy) podobnie jak z Symbolami ale tym razem w Edge są dostępne, IE niestety ich nie posiada.

I jak obiecałem [link do dema na Codepen](https://codepen.io/jcubic/pen/mBmdEV?editors=0010).
