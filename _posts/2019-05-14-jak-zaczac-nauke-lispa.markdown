---
layout: post
title:  "Jak zacząć uczyć się języka LISP"
date:   2019-05-14 01:05:54+0200
categories:
tags: lisp języki
author: jcubic
description: Wpis o tym jak zacząć naukę programowania w jęzuku LISP. Jaki dialekt wybrać Scheme czy Common LISP. Czy są jakieś kursy wideo i czy są książki o języku LISP.
image:
 url: "/img/lisp-alien.png"
 alt: "Grafika z obcym z pięcioma oczami, flagą LISP oraz napisem Made with secret alien technology"
 width: 800
 height: 500
 attribution: Jakub T. Jankiewicz, licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/4.0/). źródło na [GitHub-ie](https://github.com/jcubic/jcubic.pl/blob/master/img/lisp-alien.svg) bazuje na [Lisperati](http://www.lisperati.com/logo.html) licencja Public Domain.
related:
  -
    name: "Parser S-Wyrażeń (języka LISP) w JavaScript"
    url: "/2019/06/parser-jeyka-lisp-javascript.markdown"
sitemap:
  lastmod: 2019-06-17 17:46:56+0200
---


Dzisiaj będzie trochę inny wpis. Nie będzie dotyczył programowania stron internetowych, czyli głównego tematu
bloga. Postanowiłem napisać krótki wpis opisujący, jak zacząć naukę programowanie w języku LISP. Jest to język,
który powinien poznać każdy programista.

<!-- more -->

## Wprowadzenie

LISP to jeden z dwóch najstarszych języków programowania, który nadal jest używany. Drugi to Fortran.
Powstał on w latach 50 na MIT. Zaprojektował go [John McCarthy](https://pl.wikipedia.org/wiki/John_McCarthy).
Ciekawostką może być to, że miał być tylko teoretyczną (matematyczną) alternatywą dla
[maszyny Turinag-a](https://pl.wikipedia.org/wiki/Maszyna_Turinga). Ale jeden ze studentów McCarthy’ego
([Steve Russell](https://pl.wikipedia.org/wiki/Steve_Russell)) postanowił napisać główny element języka, czyli
funkcje `eval`. Pierwotna wersja (ta zaprojektowana przez McCarthy’ego) nie miała takiej składni
(czyli S-Wyrażeń), ale pisząc kod Russell nie mógł użyć takich samych znaków na klawiaturze, więc użył
okrągłych nawiasów. Język oparty jest na [rachunku lambda](https://pl.wikipedia.org/wiki/Rachunek_lambda)
stworzonym przez [Alonzo Churcha](https://pl.wikipedia.org/wiki/Alonzo_Church), jest to matematyczny zapis,
który powstał mniej więcej w tym samym czasie co Maszyna Turinga, jako uniwersalny język opisujący
dowolne obliczenia.

Jako zachętę zacytuje Erica Raymonda:

> "LISP is worth learning for a different reason — the profound enlightenment experience you will
> have when you finally get it. That experience will make you a better programmer for the rest of
> your days, even if you never actually use LISP itself a lot."
>
> ["How to Become a Hacker"](http://www.catb.org/~esr/faqs/hacker-howto.html)
>
> **Tłumaczenie**: "LISP jest wart nauki dla głębokiego uczucia oświecenia, kiedy go wreszcie
> zrozumiesz. To doświadczenie uczyni z ciebie o wiele lepszego programistę, nawet jeśli nie używasz
> LISPa zbyt często."
>
> ["Jak zostać Hakerem"](http://www.mkgajwer.jgora.net/ers.html)


## Krótko o kodzie LISP-a

Kod LISP-a skład się z S-Wyrażeń (listy w nawiasach gdzie elementy są oddzielone spacjami)
i ma notacje prefix-ową to znaczy, że nazwa funkcji jest pierwszym elementem listy
np.:

{% highlight scheme %}
(+ (- 10 2) 100)
{% endhighlight %}

Jest to wyrażenie, które oblicza `(10 - 2) + 100`. Co ciekawe język nie ma operatorów tylko funkcje,
które są pierwszym elementem listy. Pierwszy element to nie musi być także nazwa. Może to być
wyrażenie, które w wyniku zwróci nową funkcje czyli. tzw funkcja wyższego rzędu.

Można też zapisać.:

{% highlight scheme %}
((lambda (x y) (+ x y)) 10 20)
{% endhighlight %}

Pierwszym elementem jest wyrażenie `lambda`, czyli funkcja anonimowa, która jest od razu wywoływana.
Czyli tak jak w JavaScript IIFE. Wyrażenie `lambda` jest to specjalna konstrukcja języka, która nie
wywołuje od razu swoich argumentów, takie konstrukcje także można dodać samemu - są to makra.  Jeśli
znasz już JavaScript to nie będzie to dziwne, że funkcje mogą być typem danych. Ale LISP ma w sobie
o wiele więcej. Głównie dzięki makrom, które operują na kodzie jak na danych.  Tak jakbyśmy mieli
dostęp do parsera wewnątrz funkcji (makra). W języku JavaScript była próba dodania makr (projekt
[sweet.js](https://www.sweetjs.org/)). Projekt działa tak jak Babel, czyli konwertuje kod, który
zawiera makra, do postaci zwykłego kodu JavaScript.

## Jaki dialekt wybrać

Do wyboru są 3 główne dialekty.:

### Common LISP

Przez programistów LISP-a jest polecany jako najpotężniejszy dialekt. Ale nie polecam go na początek, ponieważ
można się zagubić w masie funkcji z biblioteki standardowej, które są w nim dostępne, a traci się możliwość
poznania istoty LISP-a.

### Scheme

Jest bardzo prosty dialekt razem z Common LISP-em dwa najczęściej używane. Powstał w latach 70 dzięki takim
ludzom jak
[Guy L. Steele](https://en.wikipedia.org/wiki/Guy_L._Steele_Jr.) i
[Gerald Jay Sussman](https://en.wikipedia.org/wiki/Gerald_Jay_Sussman). Jego zaletą jest prostota.
Mała biblioteka standardowa, więc jeśli chce się mieć podstawowe funkcje to trzeba je najpierw napisać.
Jest to świetny sposób, aby poznać język - pisanie prostych funkcji do różnych celów które mogą się przydać
w przyszłości.

### Clojure

Jest to nowoczesny dialekt LISP-a, dzięki któremu LISP stał się bardziej popularny.  W Polsce można
nawet czasami znaleźć pracę, gdzie wymagają tego języka. Działa na maszynie wirtualnej JVM (czyli to
na czym uruchamiana jest Java). Zaprojektowany przez
[Richa Hickeya](https://pl.wikipedia.org/wiki/Rich_Hickey). Więcej o języku na
[Wikipedii](https://pl.wikipedia.org/wiki/Clojure).


## Jak zacząć?

Aby zacząć naukę polecam napierw obejrzenie wykładów ok 20 godzin oglądania (10 dwu częściowych lekcji)
prowadzonego przez Abelsona i Sussmana (ten drugi zaprojektował język Scheme) prowadzone na MIT dla
pracowników HP w latach 80. Wykładowcy razem napisali książkę
[Struktura i Implementacja Programów Komputerowych](http://lubimyczytac.pl/ksiazka/135282/struktura-i-interpretacja-programow-komputerowych). Która jest obowiązkowa na studiach na MIT. Można też znaleźć w
[sieci wykłady z uniwersytetu w Berkley z roku 2010](https://www.youtube.com/watch?v=4leZ1Ca4f0g&list=PLhMnuBfGeCDNgVzLPxF9o5UNKG1b-LFY9). Osobiście oglądałem tylko te Sussmana i Abelsona.

Jeśli studiowałeś informatykę na uczelni wyższej, to nie powinno być dla ciebie problemem obejrzenie wykładów
(chyba, że jesteś z tych co olewali studia).  Jeśli nie studiowałeś i wytrzymasz wykłady i zaczynasz naukę
programowania to możesz też znaleźć masę dobrych wykładów w sieci z programowania i ogólnie informatyki z
różnych uczelni wyższych z USA.

## Pisanie kodu

Jeśli chcesz wypróbować dialekt scheme podczas oglądania wykładów, to możesz skorzystać ze strony
[repl.it](https://repl.it/languages/scheme).  Strona korzysta z jezyka scheme napisanego w JavaScript.
Zapewne wiele razy przerwiesz oglądanie, żeby wypróbować jakiś kod samemu. Jeśli nie to powinieneś chociaż
spróbować.

Jeśli chcesz spróbować napisać i wypróbować jakiś program na swoim komputerze, to polecam dwa programy
[Kawa](https://www.gnu.org/software/kawa/index.html) (do której
swoja drogą zrobiłem logo) oraz [Guile](https://en.wikipedia.org/wiki/GNU_Guile). Kawa to Scheme w języku
Java a Guile jest w C. Guile jest fajny bo można go osadzić w aplikacji w C (jest biblioteka współdzielona,
która zawiera język). Natomiast Kawa-y można np. użyć do pisania aplikacji na
[Androida](https://www.gnu.org/software/kawa/Building-for-Android.html) (tak LISP w telefonie).
Można także do tego celu użyć języka Clojure.

## Co powinieneś poznać?

Powinieneś poznać takie koncepcje jak domknięcia leksykalne (ang. closures), funkcje jako typ pierwszo-klasowy
oraz pisanie funkcji wyższego rzędu (funkcje, które zwracają funkcje, albo pobierają je jako argument).

Wszystko to jest dostępne w języku JavaScript, który dużo zapożyczył z języka Scheme.

## Kolejny krok

Jak już masz to za sobą to znaczy, że znasz już trochę LISP-a, a dokładnie dialekt scheme. Teraz polecam
książki np. wspomniana Struktura i Interpretacja Programów Komputerowych. Nie jestem pewien czy można ją
jeszcze kupić po polsku ale jest
[dostępna w oryginale na stronie MIT](https://mitpress.mit.edu/sites/default/files/sicp/index.html).
Jak już dobrze pozasz język scheme to warto poznać Common LISP-a, możesz przeczytać mój
[kurs Common LISP-a](https://jcubic.pl/jakub-jankiewicz/lisp_tutorial.php), który kiedyś napisałem.
Warto sięgnąć po książkę [Practical Common Lisp](http://www.gigamonkeys.com/book/) (w oryginale dostępna
za darmo w wersji elektornicznej). Możesz też przeczytać książkę ["Successful Lisp: How to Understand and Use Common Lisp"](https://psg.com/~dlamkins/sl/contents.html), trzeba się przedrzeć przez linki,
ale na GDrive powinien być plik z książką (nie linkuje do GDrive, bo strona może się zmienić).

## Makra

Jak już poznasz Common LISP-a i znasz scheme to możesz zacząć naukę makr, chociaż możesz też po nie sięgnąć
już po obejrzeniu wykładów SICP (ang. Structure and Interpretation of Computer Programs). Makra to jest
nalepsza rzecz jaka jest w języku LISP. Do ich nauki polecam książkę
[Let Over Lambda](https://letoverlambda.com/). Można też kupić wersje papierową z
[amazon.co.uk](https://www.amazon.co.uk/s?k=let+over+lambda&ref=nb_sb_noss). Osobiście wole czytać książki
fizyczne. Książka jest bardzo zaawansowana. Na stronie opisywana jako książka hardcorowa, ale warto. Tylko
muszę dodać, że jest poświęcona prawie w całości makrom.

Muszę też wymienić książkę, którą ostanio udało mi się dostać w wersji papierowej, a mianowicie On LISP Paula
Grahama (jest to gość od książki
[Hakerzy i Malarze](https://helion.pl/search?qa=&serwisyall=&szukaj=Hakerzy+i+malarze&wprzyg=&wsprzed=&wyczerp=),
którą polecam. Jest też założycielem inkubatora "Y Combinator" dzięki, któremu mamy stronę
[Hacker news](https://news.ycombinator.com/)). Książka jest już dawno nie drukowana i można tylko kupić
jej wersje używaną za setki złotych, ale na szczęście autor udostępnił wersje elektroniczną. Można ją
wydrukować na lulu.com [tutaj artykuł](http://www.lurklurk.org/onlisp/onlisp.html) na podstawie, którego
udało mi się wydrukować swoją na [LuluXpress](https://xpress.lulu.com/) - koszt 15 dolarów wraz z wysyłką
(w Polsce chcieli za druk ok 200zł).

## Co dalej?

Teraz to już tylko możesz dużo pisać i dużo czytać. Jak z każdym językiem. Chodzi głównie o kod. Chociaż
czytać można też artykuły. Z rzeczy które możesz jeszcze chcieć poznać to **kontynuacje** (których sam jeszcze
do końca nie rozumiem - chciałbym je dodać do swojej implementacji
[LISP-a w JavaScript](https://jcubic.github.io/lips), ale nie jestem pewien czy znam je na tyle, aby je
zaimplementować). W common LISP jest jeszcze **CLOS** czyli wbudowany system obiektowy.

Polecam też używanie edytora **GNU Emacs**, który jest częściowo napisany w języku Emacs LISP (w skrócie
ELisp) i można go rozszerzać za pomocą tego języka (fajną funkcją jest to że pod każdym skrótem klawiszowym,
nawet pojedynczym klawiszem, który wstawia literę, jest jakaś funkcja w języka ELisp, którą można dowolnie
zmienić, jest też dostępna pełna dokumentacja do każdej funkcji. A jak **budujesz ze źródła**, to możesz nawet
widzieć **kod źródłowy funkcji** samego edytora z **wewnątrz edytora**).

Możesz też spróbować sam napisać interpreter tego języka. Ja sam napisałem w języku JavaScript. Jest
udostępniony na licencji MIT i nazywa się LIPS (ang. usta, skrót rekurencyjny "LIPS Is Pretty Simple")
([link tutaj](https://jcubic.github.io/lips/)). Aktualnie próbuje go przepiać w PHP, aby móc pisać aplikacje
WWW całe w LISP-ie, mam nadzieje, że mi się uda. Projekt nazywa się LIP (czyli warga), a jest to skrót,
też rekurencyjny "LIP is Lips In Php" [link tutaj](https://github.com/jcubic/lip).

Jeśli chciałbyś napisać własny interpreter Lispa, możesz zacząć od tego artykułu:
[Parser S-Wyrażeń (języka LISP) w JavaScript](/2019/06/parser-jezyka-lisp-javascript.html).
