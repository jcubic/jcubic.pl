---
layout: post
title:  "Tryb ciemny dostępny w CSS"
date:   2019-12-18 22:30:38+0100
categories:
tags:  css css3
author: jcubic
description: Nowa funkcja w CSS do trybu ciemnego, dostępna w standardzie CSS w module Media Query Level 5.
image:
  url: "/img/gothic-cementary-skull.jpg"
  alt: "Zdjęcie czaszki na cmentarzu"
  width: 800
  height: 535
  attribution: "Jakub T. Jankiewicz, dosęp [Flickr](https://www.flickr.com/photos/jcubic/40933257345), licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/2.0/)"
---

Mamy dostęp do nowej ciekawej funkcji, która jest częścią specyfikacji CSS:
[Media Queries Level 5](https://drafts.csswg.org/mediaqueries-5/#prefers-color-scheme).
Jest już dostępna w nowoczesnych przeglądarkach.

W tym wpisie przedstawię funkcję, dzięki której można wykryć, czy strona internetowa, powinna prezentować się
w ciemniejszych kolorach.

<!-- more -->

## Media Queries

Media queries, co można przetłumaczyć jako zapytania o media. Jest to mechanizm
dzięki któremu możemy ustawić jakiś styl CSS gdy wystąpi pewne zdarzenie.
Głównie stosuje się je do styli, które reagują na zmianę rozmiaru okna przeglądarki.
(ang. Responsive Design). Można mieć inny styl na telefonie, a inny na komputerze.

## Dark Mode

Tryb ciemny (ang. Dark Mode), jest to specjalne ustawienie w przeglądarce lub systemie
operacyjnym, gdzie wszystko jest ciemne (jak sama nazwa wskazuje), jest to odwrotność domyślnego
stylu stron internetowych lub w przypadku systemu operacyjnego okien systemowych.

![Katalog na dysku w systemie Fedora GNU/Linux XFce](/img/dark-mode.jpg)

Powyższy zrzut ekranu to tryb ciemny wyświetlający katalog z repozytorium gita
tego bloga. Teraz taką samą funkcje można uzyskać także na stronach internetowych.
Będziemy mieli dwa style: jasny (domyślny) oraz ciemny dla osób, które mają takie ustawienie
systemowe.

Poniżej zrzut ekranu wyszukiwarki DuckDuckGo w trybie ciemnym.

![Strona internetowa DuckDuckGo w trybie ciemnym](/img/duck-duck-go-dark-mode.jpg)

Wsparcie tej funkcji jest dość duże, ale nawet gdyby nie było, to warto dodać
do swojej strony tryb ciemny, ponieważ nic to nie kosztuje. Najwyżej nie wszyscy
go zobaczą. Jednak osoby które mają taką funkcje włączoną, polubią bardziej twoją stronę.

Wsparcie możesz zobaczyć w serwisie
[Can I Use](https://caniuse.com/#feat=prefers-color-scheme).

Szczerze powiedziawszy jestem pozytywnie zaskoczony, spodziewałem się znacznie mniejszego wsparcia.

## Jak używać trybu ciemnego?

Po tym dłuższym wstępnie przejdźmy do kodu CSS. Poniżej kod, za pomocą którego można dodać tryb
ciemny do strony internetowej.


{% highlight css %}
@media (prefers-color-scheme: dark) {
    body {
        background-color: #2b2b2b;
        color: #797979;
    }
{% endhighlight %}

Ten kod powinien być za stylem domyślnym, aby nadpisać poprzednią wartość. Jeśli będzie przed
należałoby użyć silniejszego selektora np. `html body` albo użyć `!important`.

Można sobie ułatwić życie i w tym celu także posłużyć się [zmiennymi CSS](/2016/12/zmienne-css.html). Wystarczy
sobie zdefiniować kolory dla elementu `:root`, który będzie zawierał całą kolorystykę. wszędzie w
kodzie będzie można użyć tych zmiennych, np.:

{% highlight css %}
:root {
    --color: black;
    --background: white;
}
section {
    background: var(--background);
}
section header .nav li {
    color: var(--color);
}
@media (prefers-color-scheme: dark) {
    :root {
        --color: white;
        --background: black;
    }
}
{% endhighlight %}

Oczywiście jest to tylko prosty przykład, nie powinno się stosować stu procentowej czerni i bieli,
ponieważ wygląda to niezbyt estetycznie.

Można także używać pre-procesorów CSS, które udostępniają zmienne, aby mieć kolory w jednym miejscu.

## Podsumowanie

Tryb ciemny na stronach internetowych to ciekawa opcja, która uprzyjemni użytkowanie waszych stron,
dla niektórych użytkowników. Mimo że tylko nieliczni użytkownicy będą mogli podziwiać wygląd
strony w trybie ciemny, to warto go dodać. Szczególnie, że dodanie wcale nie jest takie trudne.
Jest o wiele prostsze niż dodanie responsywności. Kolorów na stronie zazwyczaj jest niewiele,
a jeśli nie to warto zrefaktoryzować kod CSS.

*[CSS]: Cascading Style Sheets
