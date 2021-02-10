---
layout: post
title:  "Programowanie jest jak ekspres do kawy"
date:   2020-06-10 09:13:50+0200
categories:
tags: podstawy nauka programowanie
author: jcubic
description: Jak opisać programowanie dla osób, które nie programują. Co to jest programowanie?
image:
 url: "/img/express.jpg"
 alt: "Ekspres do kawy oraz gotowa filiżanka z aromatyczną kawą"
 attribution: Źródło [PxHere](https://pxhere.com/en/photo/931338), [licencja CC0](https://creativecommons.org/publicdomain/zero/1.0/)
 width: 800
 height: 400
sitemap:
  lastmod: 2019-08-15 13:39:56+0200
---

Zastanawiałeś się kiedyś jak najlepiej opisać, czym jest istota programowania dla osób, które nie
programują? Co byś odpowiedział, gdyby ktoś cię zapytał "Co to jest programowanie?" albo
"Co tak naprawdę robią programiści?".?

W tym wpisie przedstawie, ciekawy sposób w jaki można opisać programowanie osobom, które
nie programują.

<!-- more -->

## Jak się uczymy?

Ważna rzecz, której się nauczyłem o uczeniu jest to, że łatwiej przyswajamy sobie nową wiedzę, gdy
możemy się odwołać do czegoś co już znamy. Inną ważną rzeczą jest wyobraźnia i to że łatwiej
zapamiętujemy obrazy (nawet w formie tekstu), o czym możecie przeczytać na blogu
[Poradnik Pisania](http://poradnikpisania.pl/jak-pobudzac-wyobraznie/), który bardzo polecam.

Dlatego zaraz przedstawię moją analogię dla programowania oraz różnych czynności wykonywanych przez
programistów.

## Jak przedstawić esencje programowania dla laika i nie programisty?

Funkcje w matematyce i programowaniu można porównać do ekspresu do kawy. Do takiego z górnej półki z
młynkiem na ziarna kawy, z pojemnikiem na mleko. Włączasz go i robi Ci pyszną, aromatyczną kawę
latte albo inną jaką lubisz. Jeśli nie pijesz kawy to nie szkodzi to tylko przykład.

Ważne jest to, że możesz powiedzieć "Express do kawy" i od razu wiesz, że musisz mu dostarczyć kawy,
mleka, prądu i wody. Następnie włączasz przycisk, czekasz chwilę i masz gotową kawę, którą możesz
wypić. Nie musisz się zastanawiać co dokładnie ekspres robi w środku. Możesz o tym zapomnieć,
tak samo możesz zapomnieć o tym jak robi się kawę samemu mając tylko kubek i gorącą wodę.

## Co to są funkcje w programowaniu?

Tak samo wygląda sprawa z funkcjami. W matematyce zazwyczaj (jeśli to nie jest matematyka wyższa)
masz funkcje o nazwie `f(x)` lub `g(x)`, ale w kodzie programu, możesz mieć dowolną nazwę, np. ekspres,
a zamiast nic nie mówiącego `x` możesz mieć kilka argumentów (tak samo jest w matematyce nauczanej na
studiach wyższych), tutaj będziemy mieli argumenty kawa, woda, mleko i prąd. I aby dostać kawę
wywołujesz funkcje.

Przykład kodu takiej funkcji, w języku programowania, wygląda tak:

{% highlight javascript %}
function express(water, milk, caffee, electricity) {
    // ...
    return "caffee lattte";
}
{% endhighlight %}

Jak widzisz nazwy są po angielsku, a kod jest pokolorowany, aby łatwiej było go czytać. Ważne jest,
aby ucząc się programowania, jak najszybciej przejść z pisania po Polsku, na pisanie Angielsku (albo
od razu pisać po Angielsku). Dzięki temu możemy pokazać kod szerszemu gronie ludzi, przygotuje nas
to także do pracy w międzynarodowym zespole, ale według mnie, pisanie po Polsku wygląda po prostu
nieprofesjonalnie.

Powyższy zapis funkcji jest bardzo konkretny i musi wyglądać w odpowiedni sposób, np. na zajęciach z
matematyki nie napiszesz `f(x` będzie to błąd, musi być drugi zamykający nawias. W różnych językach
programowania funkcje zapisuje się w różny sposób. Powyżej przedstawiłem przykład jednej z funkcji w
języku JavaScript.

W miejscu `// ...` jest coś co express robi, ale nas to nie obchodzi, ponieważ mamy nazwę express i
nam to wystarcza. Możemy używać jej wszędzie:

{% highlight javascript %}
var kawa = express('gorąca', 'zimne', 'ziarna', '220V');
{% endhighlight %}

## Co to jest debugowanie?

Jeśli express zrobi coś nie tak jak powinien, będziemy musieli ten expres naprawić np. oddając do
serwisu. Tak samo jest z funkcją, należy znaleźć w niej błąd i go poprawić. Proces ten nazywa się
debugowaniem, od słowa angielskiego bug czyli błąd (a dokładnie robal, nazwa prawdopodobnie wzięła
się od [historii Grace Hopper](https://pl.wikipedia.org/wiki/B%C5%82%C4%85d_(informatyka)).
Takie nadawanie nazw i pomijanie szczegółów nazywa się
[abstrakcją](https://pl.wikipedia.org/wiki/Abstrakcja_(programowanie)).

Analogia się tu jednak nie kończy. Tak samo jest, gdy pójdziesz do kawiarni i barista zrobi pyszną
kawę z listkiem z pianki.  Nawet nie musisz myśleć, o tym jak przyrządzić kawę, nie musisz myśleć o
mleku, prądzie i kawie. Robi to za ciebie barista.

![Filiżanka kawy](/img/Cappuccino_Chiang_Mai.jpeg)
<small>
Autor [Takeaway](https://commons.wikimedia.org/wiki/User:Takeaway);
Źródło [Wikipedia](https://commons.wikimedia.org/wiki/File:Cappuccino_Chiang_Mai.JPG);
Licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/3.0/deed.en)
</small>

Tak samo będzie w kodzie programu, masz funkcję `makeCoffee` i ją wywołujesz, nie musisz się
przejmować co jest w środku i co musisz dostarczyć. Ważne, że wiesz jak się nazywa i co robi.

{% highlight javascript %}
makeCoffee('late');
{% endhighlight %}

A wewnątrz tej funkcji będzie wywołanie funkcji express np.:

{% highlight javascript %}
function makeCoffee(caffee) {
   // ...
   return express(...);
}
{% endhighlight %}

## Co to jest Algorytm? Czyli Algorytm robienia kanapki.

Wyobraź sobie że robisz sobie kanapkę na śniadanie. Jest to czynność, nad którą nie musisz się
specjalnie zastanawiać. Ale co jeśli być musiał opisać sposób w jaki robot musiałby zrobić taką
kanapkę. Zakładając że robot nie jest inteligenty i wykonuje instrukcje, czyli program komputerowy,
musiałbyś wziąć wiele spraw pod uwagę. Np. gdzie znajduje się robot aktualnie, w jaki ma trafić
do kuchni, w jaki sposób trzymać i używać noża oraz jak otwierać lodówkę. Ale nawet zakładając że jest to
robot który wie jak to robić, musiałbyś mu potem powiedzieć jakie są konkretne kroki do
zrobienia kanapki. Przykład takiej listy może wyglądać tak:

* Otwórz lodówkę.
* Wyjmij żółty ser oraz masło.
* Wyjmij deskę do krojenia.
* Wyjmij chleb.
* Weź nóż.
* Połóż chleb na desce.
* Ukrój kromkę chleba.
* Odpakuj masło jeśli zapakowane.
* Nożem weź trochę masła.
* Posmaruj nożem z masłem kanapkę.
* Weź plasterek sera (zakładając że ser jest w plasterkach).
* Połóż na chlebie z masłem.

Taka lista kroków nazywa się algorytmem. Algorytm jest to rozwiązanie jakiegoś problemu, w tym
przypadku robienie kanapki przez robota. Jeśli funkcja `makeCoffee` odpowiada temu co robi barista w
miejscu `// ...` musiałby być algorytm, czyli lista kroków, które robi barista, aby zrobić kawę.

![Zdjęcie Kanapki: Algorytm robienia kanapki](/img/sandwich.jpg)
<small>
Autor [Marco Verch](https://www.flickr.com/photos/30478819@N08/);
Źródło [Flickr](https://www.flickr.com/photos/30478819@N08/47173760532);
Licencja [CC-BY](https://creativecommons.org/licenses/by/2.0/)
</small>

## Co to jest Refaktoryzacja?

Refaktoryzacja to inaczej naprawianie kodu. Czasami potrzeba jest poprawić program, aby łatwiej było go zrozumieć innym programistom,
ponieważ [jak powiedział Hal Abelson](https://en.wikiquote.org/wiki/Programming_languages):

> Programy muszą być pisane dla ludzi do czytania, i tylko przy okazji dla maszyn do uruchomienia.

Proces poprawiania istniejącego kodu programu nazywa się
[refakoryzacją](https://pl.wikipedia.org/wiki/Refaktoryzacja).

## Ciągła nauka

Istnieje powiedzenie, że człowiek uczy się całe życie. Jeśli chodzi o programistów to wiedzy jest
naprawdę dużo i każdy programista musi cały czas aktywnie się uczyć, szczególnie że wszystko szybko
się zmienia.  Nie można zostawać w miejscu. Oczywiście pewnie nie wszyscy to robią. Ale nauka to
podstawa. Nie jest to praca jak np. takiego baristy, który umie robić kawę i nie musi uczyć się niczego
nowego.

## Podsumowanie

Najważniejszą rzeczą, w programowaniu, jest **dzielenie problemu na części** i **nadawanie im
nazw**, czyli **abstrakcja**. Wszystko to po to, aby można było stworzyć skomplikowany program, ale
w danej chwili myśleć tylko o kilku małych rzeczach naraz. Ponieważ pamięć tymczasowa jest
ograniczona. Może pomieścić, w zależności od badań, od 4 do 9 rzeczy na raz.

Nie bez przyczyny nadawanie nazw jest nazywane najtrudniejszą rzeczą w informatyce. `makeCafee`
równie dobrze może się nazywać `getCoffee` lub po prostu `coffee`. Ważne jest aby programista,
w swoim programie, wiedział co ona robi.

**Ciągła nauka**, **algorytmy**, **refakoryzacja** oraz **debugowanie** też są bardzo
ważne. Najpierw trzeba poznać dziedzinę, w jakiej pisze się program. Może to być np. aplikacja,
która pomaga szpitalom w zarządzaniu, program do obsługi banku albo jakaś gra. W każdym przypadku,
gdy trzeba dodać jakąś funkcjonalność do programu, trzeba go zrozumieć, podzielić na części jeśli
jest to duży problem oraz napisać odpowiednie kroki do rozwiązania problemu czyli odpowiednie
algorytmy. Przy okazji można także podzielić większe algorytmy na kilka funkcji, które mają proste
nazwy, aby uprościć kod programu.


Co sądzisz o takiej definicji programowania?
