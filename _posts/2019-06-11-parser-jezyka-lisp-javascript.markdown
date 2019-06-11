---
layout: post
title:  "Parser S-Wyrażeń (języka LISP) w JavaScript"
date:   2019-06-11 21:52:53+0200
categories:
tags: javascript lisp
description: Do parsowania można użyć generatra parserów, ale do S-Wyrażenia czyli to z czego składa się kod języka LISP, warto napisać parser ręcznie co wcale nie jest trudne
author: jcubic
image:
  url: "/img/lisp-parser.png"
  alt: "Grafika Wektorwa z Tekstem Parser Lisp-a w JavaScript oraz grafika wektorowa przedstawiająca strukturę kodu LISP-a "
  attribution: Jakub T. Jankiewicz, licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/4.0/). Grafika bazuje na schematach blokowych z książki Struktura i Interpretacja Programów Komputerowych (SICP), źródło na [GitHub-ie](https://github.com/jcubic/jcubic.pl/blob/master/img/lisp-parser.svg)
related:
  -
    name: "Jak zacząć uczyć się języka LISP"
    url: /2019/05/jak-zaczac-nauke-lispa.html
---

S-Wyrażenia to podstawa języków rodziny [LISP](/2019/05/jak-zaczac-nauke-lispa.html). W tym wpisie
przedstawie, jak napisać prosty parser S-Wyrażeń krok po kroku. Czyli właściwie podstawę dla parsera
Lispa.  Można by użyć do tego celu
[generatora parserów](/2017/11/parsery-i-generatory-parserow-w-javascript.html), ale prościej jest
napisać parser samemu. Użyjemy do tego celu języka JavaScript.

<!-- more -->

## Co to są S-Wyrażenia?

Jeśli nie miałeś jeszcze styczności z językiem LISP to S-Wyrażenia wyglądają tak:

{% highlight lisp %}
(+ (second (list "xxx" 10)) 20)
{% endhighlight %}

Jest to format danych, gdzie wszystko składa się z atomów albo list otoczonych nawiasami (gdzie
atomy są oddzielone spacjami).

Dodatkowo S-Wyrażenia mogą mieć dodatkowe typy danych, dokładnie tak jak JSON czyli:
* liczby
* symbole - które można dowolnie interpretować np. wartość `"true"` albo `"t"`, może odpowiadać `true`
  z JavaScript.
* ciągi znaków

Kod lispa składa się z S-Wyrażeń, ale to nie jest wszystko do czego mogą służyć. Nadają się doskonale
jako format wymiany danych.

S-Wyrażenia ją np. używane jako
[zapis WASM, który można przeczytać](https://developer.mozilla.org/en-US/docs/WebAssembly/Understanding_the_text_format).
Prawdopodobnie ze wzlędu na prostotę parsowania, oraz na to, że nie trzeba było wymyślać swojego
własnego formatu.

Można go np. użyć do komunikacji między serwerem a przeglądarką. Można ich używać zamiast np. JSON-a.

Dodatkowo wyrażenie może mieć specjalny znak kropkę `.` która tworzy parę

{% highlight lisp %}
(1 . b)
{% endhighlight %}

To para dwóch elementów (nie jestem pewien czy to także część S-Wyrażeń czy już języka LISP).

Jest to alternatywny zapis struktury listy, który bardziej mówi jak dokładnie wyglądają dane i np. listę

{% highlight lisp %}
(1 2 3 4)
{% endhighlight %}

Można przedstawić jako

{% highlight lisp %}
(1 . (2 . (3 . (4 . nil))))
{% endhighlight %}

Dzięki takiemu zapisowi można tworzyć dowolne drzewa binarne. W każdym razie my nie będziemy
obsługiwać tego typu S-Wyrażeń, aby nie komplikować parsera.

## Tokenizer

Najpierw musimy podzielić ciąg znaków na tokeny, czyli nawiasy, ciągi znaków oraz atomy.  Tak
działają np. niektóre generatory parserów (np. sławna para lex i yacc oraz flex i bison, ta druga
para to wolne oprogramowanie, część projektu GNU).

Najprościej jest to wykonać za pomocą wyrażeń regularnych.

Najprostszy przykład to:

{% highlight javascript %}
'(foo bar (baz))'.split(/(\(|\)|\n|\s+|\S+)/);
{% endhighlight %}

i to prawie działa. Pierwszy problem to puste ciągi znaków, między dwoma dopasowaniami oraz na
początku i końcu. czyli np. dla:

{% highlight javascript %}
'(('.split(/(\(|\)|\n|\s+|\S+)/);
// ["", "(", "", "(", ""]
{% endhighlight %}

mamy 5 znaków zamiast 2.

Można to rozwiązać za pomocą funkcji `filter` na wynikowej liście (tablicy).

{% highlight javascript %}
'(('.split(/(\(|\)|\n|\s+|\S+)/).filter(token => token.length);
// ["(", "("]
{% endhighlight %}

nie będziemy potrzebowali też spacji więc one także można usunąć:

{% highlight javascript %}
'(   ('.split(/(\(|\)|\n|\s+|\S+)/).filter(token => token.trim().length);
// ["(", "("]
{% endhighlight %}

Drugi bardziej poważny problem to `baz))` jako ostatni token, to przykład:

{% highlight javascript %}
'(foo bar (baz))'.split(/(\(|\)|\n|\s+|\S+)/).filter(token => token.trim().length);
// ["(", "foo", "bar", "(", "baz))"]
{% endhighlight %}

problemem jest wyrażenie `\S+` które dopasowuje zachłannie. Aby to naprawić należy użyć, takiego wyrażenia:

```
[^\s()]+
```

czyli wszystko co nie jest nawiasem i białym znakiem (czyli to samo co `\S+` tylko bez nawiasów).

Zapiszmy nasz tokenizer jako funkcje:

{% highlight javascript %}
var tokens_re = /(\(|\)|\n|\s+|[^\s()]+)/;

function tokenize(string) {
    string = string.trim();
    if (!string.length) {
        return [];
    }
    return string.split(tokens_re).filter(token => token.trim());
}
{% endhighlight %}

nie musimy używać `length` ponieważ pusty ciąg znaków także jest konwertowany do wartości `boolean` i ma
wartość `false`.

Ale co z ciągami znaków? Jeśli np. spróbujemy przetworzyć taki ciąg:

{% highlight javascript %}
tokenize(`(define (square x)
            "funkcja foo wywołanie (foo 10) zwraca 100"
            (* x x))`);
// ["(", "define", "(", "square", "x", ")", ""funkcja", "foo", "wywołanie",
//  "(", "foo", "10", ")", "zwraca", "100"", "(", "*", "x", "x", ")", ")"]
{% endhighlight %}

(jest to funkcja w dialekcie Scheme języka LISP) Użyto tutaj szablonów ciągów znaków (ang. template
literals), aby można było zapisać znaki nowej linii wewnątrz kodu.

To co jest wewnątrz ciągu znaków nam się już rozsypie (czyli
`"funkcja square wywołanie (square 10) zwraca 100"`).

## Wyrażenie regularna dla ciągów znaków

Należy dodać ciągi znaków jako wyjątek, najlepiej jako pierwszy element naszego wyrażenia regularnego.

Wyrażenie które dopasowuje się do ciągów znaków może wyglądać tak:

{% highlight javascript %}
/"[^"\\]*(?:\\[\S\s][^"\\]*)*"/
{% endhighlight %}

A oto jak będzie wyglądało nasze wynikowe wyrażenie:

{% highlight javascript %}
var tokens_re = /("[^"\\]*(?:\\[\S\s][^"\\]*)*"|\(|\)|\n|\s+|[^\s()]+)/;
{% endhighlight %}

Można by też dodać komentarze lispowe, ale ze względu na to, że nie jest to parser języka LISP, tylko
S-Wyrażeń, nie będziemy dodawali komentarzy. Tak samo jak nie ma ich w formacje JSON (dodanie ich
nie powinno być problemem).

Teraz nasz tokenizer zwraca poprawny wynik:

{% highlight javascript %}
tokenize(`(define (square x)
            "funkcja square wywołanie (square 10) zwraca 100"
            (* x x))`);
// ["(", "define", "(", "square", "x", ")",
//  ""funkcja square wywołanie (square 10) zwraca 100"",
//  "(", "*", "x", "x", ")", ")"]
{% endhighlight %}

I to cały `tokenizer`.

## Parser

Jako że budujemy drzewo, tworząc nasz parser możemy się posłużyć stosem (czyli LIFO - ang. Last In
First Out).

Aby w pełni zrozumieć działanie parsera dobrze jest wcześniej znać podstawowe struktury danych, tj.
Listy jedno kierunkowe, drzewa binarne oraz Stos.

Oto pierwsza wersja parsera.

{% highlight javascript %}
function parse(string) {
    var tokens = tokenize(string);
    var result = []; // as normal array
    var stack = []; // as stack
    tokens.forEach(token => {
        if (token == '(') {
            stack.push([]); // add new list to stack
        } else if (token == ')') {
            if (stack.length) {
                // top of the stack is already constructed list
                const top = stack.pop();
                if (stack.length) {
                    // add constructed list to previous list
                    var last = stack[stack.length - 1];
                    last.push(top);
                } else {
                    result.push(top); // fuly constructed list
                }
            } else {
                throw new Error('Syntax Error - unmached closing paren');
            }
        } else {
            // found atom add to top of the stack
            // top is used as array we only add at the end
            const top = stack[stack.length - 1];
            top.push(token);
        }
    });
    if (stack.length) {
        throw new Error('Syntax Error - expecting closing paren');
    }
    return result;
}
{% endhighlight %}

Funkcja tworzy tablicę z naszą strukturą w formie tablic. Jeśli będziemy parsowali więcej niż jedno
S-Wyrażenie, będziemy mieli więcej elementów w tablicy:

{% highlight javascript %}
parse(`(1 2 3) (1 2 3)`)
// [["1", "2", "3"], ["1", "2", "3"]]
{% endhighlight %}

Mimo że nie obsługujemy kropki, czyli S-Wyrażeń w formie:

```
((foo . 10) (bar . 20))
```

Nie musimy tworzyć specjalnej struktury dla naszej sparsowanej listy, aby mieć poprawny Parser.
(Taki parser może być np. podstawą jakiegoś interpretera Lispa). Ale dobrze od razu mieć strukturę,
w której będziemy przechowywać nasze S-Wyrażenia, będzie to konstruktor `Pair`. Z którego możemy
zbudować drzewo S-Wyrażeń (czyli drzewo binarne). Umożliwi nam to tworzenie dowolnych drzew.

{% highlight javascript %}
function Pair(head, tail) {
   // make sure that we return object even if Pair is called like function
   if (typeof this !== 'undefined' && this.constructor !== Pair ||
       typeof this === 'undefined') {
       return new Pair(head, tail);
   }
   this.head = head;
   this.tail = tail;
}
{% endhighlight %}

Musimy mieć też coś, co będzie reprezentować pustą listę, zazwyczaj w języku LISP jest to nil.

{% highlight javascript %}
function Nil() {}
var nil = new Nil();
{% endhighlight %}

możemy napisać funkcje, która konwertuje tablicę na tą strukturę:

{% highlight javascript %}
Pair.fromArray = function(array) {
    if (!array.length) {
        return nil;
    }
    var head;
    if (array[0] instanceof Array) {
        head = Pair.fromArray(array[0]);
    } else {
        head = array[0];
    }
    return new Pair(head, Pair.fromArray(array.slice(1)));
}
{% endhighlight %}

Aby dodać to do naszej funkcji parsera, wystarczy wstawić na końcu (oczywiście razem z `return`):

{% highlight javascript %}
result.map(Pair.fromArray);
{% endhighlight %}

Niestety jeżeli chciałbyś dodać później operator kropki, to do tworzenia pary musiałbyś już tworzyć
strukturę ręcznie, wewnątrz parsera.

Nie konwertujemy samej tablicy `result`, ponieważ jest to tylko kontener na S-Wyrażenia, które są
w środku.  Każdy element tej tablicy powinien być listą, więc możemy użyć funkcji `Array::map`.

sprawdźmy jak to działa

{% highlight javascript %}
parse('(1 (1 2 3))')
{% endhighlight %}

Wynikiem będzie taka struktura (jest to wynik `JSON.stringify` z wstawionymi wartościami `nil`).

{% highlight javascript %}
{
    "head": "1",
    "tail": {
        "head": {
            "head": "1",
            "tail": {
                "head": "2",
                "tail": {
                    "head": "3",
                    "tail": nil
                }
            }
        },
        "tail": nil
    }
}
{% endhighlight %}

Warto jeszcze napisać funkcje `toString` dla obiektów typu Pair.

{% highlight javascript %}
Pair.prototype.toString = function() {
    var arr = ['('];
    if (this.head) {
        var value = this.head.toString();
        arr.push(value);
        if (this.tail instanceof Pair) {
            // replace hack for nested list because structure is a tree
            // and here tail is next element
            var tail = this.tail.toString().replace(/^\(|\)$/g, '');
            arr.push(' ');
            arr.push(tail);
        }
    }
    arr.push(')');
    return arr.join('');
};
{% endhighlight %}

Sprawdźmy jak to działa:

{% highlight javascript %}
parse("(1 (1 2 (3)))")[0].toString()
// "(1 (1 2 (3)))"
{% endhighlight %}

Został nam jeszcze jeden problem, wynikowa struktura nie ma liczb, tylko wszystko jest ciągiem znaków:

## Parsowanie atomów

Najpierw parowanie liczb, do tego celu użyjemy tych wyrażeń (znalezione gdzieś w sieci):

{% highlight javascript %}
var int_re = /^[-+]?[0-9]+([eE][-+]?[0-9]+)?$/;
var float_re = /^([-+]?((\.[0-9]+|[0-9]+\.[0-9]+)([eE][-+]?[0-9]+)?)|[0-9]+\.)$/;


if (atom.match(int_re) || atom.match(float_re)) {
    // in javascript every number is float but if it's slow you can use parseInt for int_re
    return parseFloat(atom);
}
{% endhighlight %}

Dalej parsowanie ciągów znaków. Nasze ciągi, są prawie takie same jak te z JSON-a, tylko z jednym
wyjątkiem mogą mieć nowe linie (tak zazwyczaj jest w językach LISP).  Aby użyć funkcji `JSON.parse`
można zamienić nową linie na slash i n czyli `\n` na `\\n`.

{% highlight javascript %}
if (atom.match(/^".*"$/)) {
   return JSON.parse(atom.replace(/\n/g, '\\n'));
}
{% endhighlight %}

Dzięki temu dostajemy za darmo, obsługę wszystkich znaków ucieczki np, `\t` oraz znaków Unicode.
Zawsze warto jest korzystać z udogodnień języka w którym pisany jest parser.

Kolejnym elementem S-Wyrażeń są symbole, czyli dowolny ciąg nie będący ciągiem znaków (tj. bez
cudzysłowów). Możemy utworzyć konstruktor `LSymbol`, aby odróżnić go od funkcji `Symbol` z języka
JavaScript.

{% highlight javascript %}
function LSymbol(name) {
    this.name = name;
}
LSymbol.prototype.toString = function() {
    return this.name;
};
{% endhighlight %}

całość funkcji parsującej atomy może wyglądać tak:

{% highlight javascript %}
function parseAtom(atom) {
    if (atom.match(int_re) || atom.match(float_re)) { // numbers
        return parseFloat(atom);
    } else if (atom.match(/^".*"$/)) {
       return JSON.parse(atom.replace(/\n/g, '\\n')); // strings
    } else {
       return new LSymbol(atom); // symbols
    }
}
{% endhighlight %}

Nasz parser z dodaną funkcja `parseAtom`:

{% highlight javascript %}
function parse(string) {
    var tokens = tokenize(string);
    var result = [];
    var stack = [];
    tokens.forEach(token => {
        if (token == '(') {
           stack.push([]);
        } else if (token == ')') {
           if (stack.length) {
               const top = stack.pop();
               if (stack.length) {
                  var last = stack[stack.length - 1];
                  last.push(top);
               } else {
                  result.push(top);
               }
           } else {
               throw new Error('Syntax Error - unmached closing paren');
           }
        } else {
           const top = stack[stack.length - 1];
           top.push(parseAtom(token)); // this line was added
        }
    });
    if (stack.length) {
        throw new Error('Syntax Error - expecting closing paren');
    }
    return result.map(Pair.fromArray);
}
{% endhighlight %}

Można także poprawić funkcje `toString` dla obiektów `Pair`, aby używała `JSON.stringify` dla ciągów
znaków aby odróżnić je od symboli:

{% highlight javascript %}
Pair.prototype.toString = function() {
    var arr = ['('];
    if (this.head) {
        var value;
        if (typeof this.head === 'string') {
            value = JSON.stringify(this.head).reaplce(/\\n/g, '\n');
        } else {
            value = this.head.toString(); // any object including Pair and LSymbol
        }
        arr.push(value);
        if (this.tail instanceof Pair) {
            // replace hack for nested list because structure is a tree
            // and here tail is next element
            var tail = this.tail.toString().replace(/^\(|\)$/g, '');
            arr.push(' ');
            arr.push(tail);
        }
    }
    arr.push(')');
    return arr.join('');
};
{% endhighlight %}

I oto cały parser. Pozostały nam jeszcze wartości `true` oraz `false` (oraz ewentualnie `null`).
Zostawiam to jako ćwiczenie dla czytelnika.


*[WASM]: WebAssembly
*[GNU]: GNU's Not Unix
