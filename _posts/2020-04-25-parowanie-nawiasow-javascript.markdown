---
layout: post
title:  "Jak parować nawiasy lub inne znaki w JavaScript?"
date:   2020-04-25 02:20:24+0200
categories:
tags: javascript parsery
description: Czasami musimy sprawdzić czy mamy poprawnie otwarte i zamknięte nawiasy. Tak jak w przypadku parsowania HTML, nie powinno się do tego używać wyrażeń Regularnych.
author: jcubic
image:
  url: "/img/laptop-vector-graphic.png"
  alt: "Grafika Wektorwa z Tekstem Parowanie Nawiasów w JavaScript oraz grafika wektorowa przedstawiająca Laptopa oraz dwa wyrażenia z nawiasami"
  attribution: Jakub T. Jankiewicz, licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/4.0/)
---

Pisząc mój interpreter języka Lisp (dokładnie dialektu Scheme) o nazwie
[LIPS](https://jcubic.github.io/lips/), zdecydowałem się dodać obsługę nawiasów kwadratowych,
ponieważ niektóre książki do języka Scheme, mieszają nawiasy okrągłe oraz kwadratowe.
Jednak aby nie komplikować parsera, nie sprawdzał on, czy nawiasy do siebie pasują i np. można było
wywołać kod z pomieszanymi nawiasami.

W tym wpisie przedstawię jak napisać poprawne parowanie nawiasów, czyli funkcję, która może się
przydać nie tylko przy parsowaniu języka Lisp. [Spoiler] Do tego celu nie warto nawet próbować,
używać Wyrażeń Regularnych (RegExp).

<!-- more -->

Zasada parowania nawiasów jest prosta, podobna do tej, którą mieliśmy w przypadku
[parsera S-Wyrażeń](/2019/06/parser-jezyka-lisp-javascript.html), czyli podstawy języka LISP.
Tylko w przypadku parowania, musimy wziąć pod uwagę tylko nawiasy, możemy zignorować pozostałe tokeny.

## Stos

Do parowania najprościej jest użyć stosu. Stos to jedna z najprostszych struktur danych. Mamy dwie
podstawowe operacje odkładania na stosie (`push`) oraz zdejmowania (`pop`). Działa to analogicznie do stosu
książek. Ostatni odłożony na stosie będzie pierwszym w kolejności do zdjęcia. Dzięki stosowi łatwiej
jest przetwarzać (parsować) znaki, które mają swój początek i koniec jak np. S-Wyrażenia, zwykłe
nawiasy czy znaczniki XML.

Obie metody (czyli `push` oraz `pop` posiadają także tablice, które można używać jak stosu), ale
warto napisać sobie prostą funkcja/konstruktor (lub klasę) jako abstrakcję stosu, aby uprościć kod i
lepiej nazwać operacje na tej strukturze danych. Jak np. sprawdzanie co znajduje się na szczycie
stosu.

{% highlight javascript %}
function Stack() {
  this.data = [];
}
Stack.prototype.push = function(item) {
  this.data.push(item);
};
Stack.prototype.top = function() {
  return this.data[this.data.length - 1];
};
Stack.prototype.pop = function() {
  return this.data.pop();
};
Stack.prototype.is_empty = function() {
  return !this.data.length;
};
{% endhighlight %}

> Użyłem funkcji konstruktora oraz prototypu, ponieważ nie lubię klas ES6 (ES2015).

## Algortym parowania nawiasów

Teraz przedstawię algorytm (czyli opis kroków) parowania nawiasów:

Musimy mieć pętle przechodzącą po tokenach, najlepiej po samych nawiasach.

Gdy mamy otwierający nawias, musimy wrzucić element na stos.  Gdy mamy zamykający nawias, musimy
sprawdzić, czy ostatni na stosie to pasujący znak (odpowiednik zamykającego).  Jeśli tak zdejmujemy
go ze stosu. Jeśli nie to znaczy, że mamy pomieszane nawiasy i musimy zwrócić wyjątek.  Jeśli mamy
zamykający nawias, ale nie ma nic na stosie, to także musimy zwrócić wyjątek, ponieważ nie ma
otwierającego nawiasu, który pasuje to tego zamykającego.

Po sprawdzeniu wszystkich znaków (tokenów), jeśli coś jest na stosie, to znaczy, że nie zamknęliśmy
wszystkich nawiasów, ale taki przypadek jest poprawny, dlatego w tym przypadku po prosu zwracamy
`false`, a nie wyjątek.

Jeśli stos jest pusty to zwracamy `true`. Oznacza to, że mamy poprawnie skończone wyrażenie. Gdyby
to było S-Wyrażenie moglibyśmy użyć parsera, aby je przetworzyć i nie musielibyśmy się martwić o
niepoprawny wynik (oczywiście jeśli parser jest poprawnie napisany).

## Kod funkcji parowania nawiasów

Poniżej kod funkcji (użyliśmy funkcji tokenize, którą możesz zobaczyć we wpisie o
[parsowaniu S-Wyrażeń](/2019/06/parser-jezyka-lisp-javascript.html)).

{% highlight javascript %}
function balanced(str) {
    // pasujące nawiasy
    var maching_pairs = {
        '[': ']',
        '(': ')',
        '{': '}'
    };
    var open_tokens = Object.keys(maching_pairs);
    var brackets = Object.values(maching_pairs).concat(open_tokens);
    // usuwamy to co nie jest nawiasem
    const tokens = tokenize(str).filter(token => brackets.includes(token));
    const stack = new Stack();
    for (const token of tokens) {
        if (open_tokens.includes(token)) {
            stack.push(token);
        } else if (!stack.is_empty()) { // czy jest to zamykający znak? bo nie jest otwierający
            var last = stack.top();
            // ostatni otwierający znak na stosie musi pasować do znaku zamykającego
            const closing_token = maching_pairs[last];
            if (token === closing_token) {
                stack.pop();
            } else {
                // nie pasujący znak
                throw new Error(`Sytnax error: missing closing ${closing_token}`);
            }
        } else {
            // jest to jeszcze jeden przypadek gdy mamy znak zamykający, ale nie było otwierajacego
            throw new Error(`Sytnax error: not matched closing ${token.token}`);
        }
    }
    return stack.is_empty();
}
{% endhighlight %}

Kod jest o wiele prostszy niż parser języka LISP (S-Wyrażeń), ponieważ nie musimy przetwarzać
wszystkich znaków (tokenów). Parser Lispa także używał stosu.

## Podsumowanie

Nie warto nawet zaczynać przetwarzać wyrażeń, które mają swoje otwierające i zamykające znaki, za pomocą
wyrażeń regularnych. Na Stack Overflow jest słynne pytanie:

["RegEx match open tags except XHTML self-contained tags"](https://stackoverflow.com/q/1732348/387194)

I odpowiedź trochę w stylu Monty Pythona (ciężko to opisać trzeba przeczytać).

Parowanie nawiasów to dokładnie taki sam problem, jak parsowanie HTML. Jak widać z załączonego
kodu, jest to zadanie dość proste, gdy używa się stosu. Możliwe że da się napisać takie Wyrażenie
Regularne, które sprawdzi czy ciąg znaków ma sparowane nawiasy. Ale sprawa się komplikuje, gdy
np. mamy ciągi znaków i nawiasy wewnątrz powinny być ignorowane. Rozwiązanie okazuje się jednak
proste, gdy używamy właściwych narzędzi.

Osobiście uwielbiam Wyrażenia Regularne, ale warto zawsze się zastanowić, czy jest to dobre narzędzie
do danego celu.

Jeśli masz jakiś pomysł, do czego jeszcze można by użyć funkcji parującej nawiasy lub może używasz
już takiej funkcji gdzieś w swoim kodzie albo masz zamiar, koniecznie napisz w komentarzu.
