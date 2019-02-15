---
layout: post
title:  "Obserwujemy wszystko w Przeglądarce"
date:   2019-02-04 10:00:32+0100
categories:
tags: javascript
author: jcubic
description: O tym jakie nowe API są dostępne w przeglądarkach, które umożliwiają podsłuchiwanie zdarzeń.
image:
 url: "/img/magnifying-glass-02.jpg"
 alt: "Lupa i książka"
 width: 800
 height: 529
---


W przeglądarkach występują różne zdarzenia. Są one asynchroniczne, mimo że przeglądarka jest jednowątkowa, pomijając wątki worker'ów.
Możemy się podpiąć pod te mechanizmy zdarzeń za pomocą różnych API dostępnych w przeglądarkach.

<!-- more -->

Z chwilą pisania tego artykułu mamy dostęp do 4 obserwatorów:

* IntersectionObserver
* PerformanceObserver
* MutationObserver
* ResizeObserver

Oraz

* Object.observe, API które zostało usunięte (zaimplementowały je tylko Chrome oraz Opera)
  [can I use](https://caniuse.com/#feat=object-observe).

* Jest jeszcze jeden mechanizm a jest nim obiekt Observable.

## Zdarzenia przecinania się elementów

Nowy InteresectionObserver jest to mechanizm dzięki, któremu możemy określić czy dwa elementy nachodzą na siebie.
Zdarzenie wywołuje się gdy procent (a dokładnie stosunek - ratio) przecinania się obiektów się zmienia.

Fajną funkcją intersection obserwatora jest to, że można go także użyć, aby wywołać zdarzenie, gdy element zniknie lub
się pojawi.  Można to osiągnąć sprawdzając przecięcie obiektu z elementem body.

## Zdarzenie gdy obiekt pojawia się i/lub zniknie

{% highlight javascript %}
var element = document.querySelector('.element');
var pre = document.querySelector('pre');

// nasłuchujemy na customowy event
element.addEventListener('visibility', (e) => console.log(e.detail.visible), false);

if (window.IntersectionObserver) {
    var observer = new IntersectionObserver(function(entries) {
        var event = new CustomEvent(
            "visibility", {
                detail: {
                    visible: !!entries[0].intersectionRatio
                },
                bubbles: true,
                cancelable: true
            }
        );
        // wywołujemy event gdy stan się zmieni
        element.dispatchEvent(event);
    }, {
        root: document.body
    });
    // musimy włączyć nasłuchiwanie
    observer.observe(element);
    // gdy nie potrzebujemy już obserwera wywołujemy
    // observer.unobserve(element);
}
// event, który włącza lub wyłącza klasę hidden (która powinna być ustawiona w css)
document.querySelector('button').addEventListener('click', () => {
    element.classList.toggle('hidden');
})
{% endhighlight %}

Gdy klikamy na przycisku, zmienia się klasa `hidden`, i gdy jest ona dodana do css i chowa element, wywoła się
event. Nastąpi to także przy inicjacji.

Ten obserwator jest bardzo ważny z powodu optymalizacji działania aplikacji. Ponieważ bez niego zazwyczaj,
aby sprawdzić czy element nakłada się na inny, stosuje się funkcję `getBoundingClientRect`, która z niewiadomych przyczyn
uruchamia mechanizm layoutu strony (czyli tzw. reflow). Lista funkcji, które wywołują reflow, można znaleźć na gist'cie
[Paula Irisha](https://gist.github.com/paulirish/5d52fb081b3570c81e3a).

Innym ciekawym zastosowaniem jest sprawdzanie czy element znajduje się w widoku strony, można np. ładować obrazki
tzw. metoda leniwego ładowania (ang. lazy load). Przykład w tym
[artykule](https://webdesign.tutsplus.com/tutorials/how-to-intersection-observer--cms-30250).


## Zdarzenia związane z wydajnością strony

Drugim obserwatorem jest obserwator wydajności. czyli
[PerformanceObserver](https://developer.mozilla.org/en-US/docs/Web/API/PerformanceObserver).

{% highlight javascript %}
function perf_observer(list, observer) {
    console.log(list);
}
var observer = new PerformanceObserver(perf_observer);
observer.observe({entryTypes: ["measure"]});
{% endhighlight %}

Głównym zadaniem tego obserwatora jest jedno miejsce, które będzie zbierać dane o wydajności strony.
Czyli gdy np. wywołamy funkcje `performance.mark()`, które startuje marker oraz `performance.measure()`
który mierzy czas od markera do momentu wywołania, dostaniemy odpowiednie zdarzenie w obserwatorze.

Można użyć go także do sprawdzania kiedy wywoła się rysowanie (ang. paint) strony. Oraz w celu sprawdzania czy jakieś
zadanie nie wywołuje się za długo. Nie wiem jednak dlaczego typ longtask nie pojawia się na
[liście typów dla tego obserwatora na MDN](https://developer.mozilla.org/en-US/docs/Web/API/PerformanceEntry/entryType).
Może dlatego że ma [swoją własną specyfikacje](https://github.com/w3c/longtasks), jeśli wiesz napisz w komentarzu.

Obserwator ma trochę inną składnie, i aby przestać obserwować należy wywołać:

{% highlight javascript %}
observer.disconnect();
{% endhighlight %}

## Zmiany elementu w DOM

**MutationObserver** czyli obserwator który reaguje na zmiany w drzewie DOM to jeszcze jeden bardzo ciekawy
obserwator. Można go np. użyć aby wykryć czy dodano atrybut do elementu lub czy element zmienił swoją zawartość. Można
by go np, użyć aby wywołać jakąś akcje, gdy komponent, razem ze swoimi dziećmi, w AngularJS się wyrenderuje, co czasami
sprawia problemy.

A oto przykład użycia

{% highlight javascript %}
var mutations = new MutationObserver(function(mutations) {
   console.log('coś zmodyfikowało element');
});
var node = document.querySelector('.element');
mutations.observe(node, {
   childList: true,
   subtree: true,
   attributes: true
});

// aby wyłączyć obsweratora
// observer.disconnect();
// obserwator wywoła się gdy np. wywołamy
node.setAttribute('data-foo', 'bar');
{% endhighlight %}

Listę opcji można znaleźć na [MDN](https://developer.mozilla.org/en-US/docs/Web/API/MutationObserverInit).

## Resize na elemencie

Jest to coś na co wiele osób, od kiedy pojawiło się jQuery, długo czekało, czyli zdarzenie resize na dowolnym elemencie.

API jest takie samo jak IntersectionObserver'a, przy czym pierwszy argument jaki dostaje funkcja jest to tablica,
obiektów, które zawierają **target** oraz **contentRect**. Dzięki któremu można pobrać szerokość i wysokość elementu bez
potrzeby wywoływania ponownego renderowania strony (wspomniany wcześniej reflow).

{% highlight javascript %}
if (window.ResizeObserver) {
    var resizer = new ResizeObserver(function(entry) {
        console.log(entry[0].target, entry[0].contentRect.width, entry.contentRect.height);
    });
    resizer.observe(element);
    // observer.unobserve(element);
}
{% endhighlight %}

Wsparcie jest jednak bardzo słabe, z chwilą pisania tego artykułu tylko Opera oraz Chrome go zaimplementowały.
Możesz zobaczyć wsparcie na [Can I Use](https://caniuse.com/#feat=resizeobserver).

Moim zdaniem najlepsza alternatywa, która jak do tej pory działała w każdych warunkach, to dodanie do elementu
iFrame'a który wypełni cały element za pomocą css, ale będzie nie widoczny (np, za pomocą `top: -100%`) na tym
elemencie można następnie ustawić normalne zdarzenie resize. Ale gdy przeglądarka to umożliwia warto zastosować
obserwatora zmiany rozmiaru.

## Uniwersalny obserwator

To ostanie omawiane API czyli Observable. Jest to API które jeszcze nie jest (z chwilą pisania tego artykułu)
zaimplementowane w żadnej przeglądarce. Tutaj możesz znaleźć
[propozycje tego API](https://github.com/tc39/proposal-observable).

Jest to dość znany wzorzec projektowy czyli Publish/Subscribe (w skrócie pubsub) dostępny w wielu bibliotekach.
Nic nadzwyczajnego ponieważ, nie ma problemu napisanie Polyfill'a, ponieważ nie dodaje żadnej nowej składni.
Ciekawi mnie czy jednak przeglądarki go zaimplementują. Jego mocną stroną byłoby to, że nie potrzebna by była
żadna dodatkowa biblioteka.

Jeśli chcesz się pobawić Obserwatorem zmiany rozmiaru oraz API Observable, możesz zobaczyć to w tym
[DEMO](https://codepen.io/jcubic/pen/rPwMJG?editors=1011). Do strony dodano polyfill.

## Zdarzenie bezczynności

Oprócz obserwatorów jest jeszcze zdarzenie, gdy przeglądarka nic nie robi. Nie jest to obserwator, ale pomyślałem, że
warto o nim wspomnieć. `requestIdleCallback` działa tak jak `requestAnimationFrame`, tylko funkcja zwrotna zostanie
wywołana, gdy zakładka z naszą stroną lub aplikacją internetową, w dane chwili nie będzie nic robić.  Niestety funkcję
[wspierają tylko Chrome, Firefox oraz Opera](https://caniuse.com/#feat=requestidlecallback).


## Podsumowanie

Obserwatory to nowe API, które zaczęły pojawiać się w przeglądarkach. Dodają nowe funkcjonalności do naszych
aplikacji internetowych. Funkcjonalności, które nie było łatwo zaimplementować bez nich. Mam nadzieje że nie podzielą
losu `Object.observe` i na stałe zagoszczą w przeglądarkach.

Czy miałeś okazje już użyć, któregoś z tych API w produkcyjnej aplikacji, albo w bibliotece Open Source? Koniecznie
napisz w komentarzu. Mi zdarzyło się już użyć 3 Obserwatorów: Mutation, Resize oraz Intersection w jednym projekcie
Open Source oraz Mutation Observer w aplikacji produkcyjnej Shiny (aplikacja w języku R).
