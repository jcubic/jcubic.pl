---
layout: post
title:  "Dlaczego warto znać AngularJS i jQuery w 2017"
date:   2017-12-09 12:24:14+0100
categories:
tags:  javascript biblioteki jquery angularjs angular
author: jcubic
description: W roku 2017 mało kto, kto tworzy nową aplikacje korzysta z jQuery albo AngularJS. Zaczynając od zera warto korzystać z najnowszego Reacta, Vue.js lub Angulara 2, ale na pewno warto znać tą bibliotekę i framework.
---

W roku 2017 mało kto, kto tworzył nową aplikacje korzystał z jQuery albo AngularJS. W 2018 roku tworząc aplikacje od początku warto skorzystać z najnowszego Reacta, Vue.js lub Angulara 2 (z chwilą pisania tego artykułu Angular 5).

Oto 5 powodów, dla których nadal warto znać Angulara 1.x czyli AngularJS oraz jQuery.

<!-- more -->

## Masz większe szanse na rynku pracy

Mimo, że tworząc aplikacje, można skorzystać z frameworka architektonicznego, to jQuery jest wykorzystywany przez miliony stron i jeśli będziesz szukał pracy, będziesz mógł wybierać z większej liczby ofert, gdy projekt będzie to jakaś stara aplikacja. Istnieją także frameworki, które korzystają z jQuery i jest to jedyny wybór jak np. [framework Shiny](https://shiny.rstudio.com/) w języku programowania R, gdzie reaktywność jest po stronie serwera a całość jest napisana za pomocą jQuery i web socketów.

Jeśli oferta pracy wymaga znajomości Angulara, a nie wymaga TypeScript (w którym tworzy się aplikacje w Angular 2) to powodów mogą być dwa: przeoczenie albo projekt dotyczy Angulara 1.x czyli AgularJS.

Ale to tylko jeśli szukając pracy rozważasz oferty, które dotyczą starego kodu (ang. legacy code).

## Istnieje ogromna liczba gotowych rozwiązań

Dzięki temu, że jQuery był (lub może nadal jest, dane wskazują tylko jak dużo stron korzysta z tej biblioteki) o wiele popularniejszy niż Angular, istnieje o wiele więcej pluginów jQuery niż modułów AngularJS czy Angular 2. Jest to spowodowane tym, że zwykłych stron internetowych jest o wiele więcej niż aplikacji internetowych.

AngularJS fanie się także komponuje z jQuery gdy trzeba zrobić coś bardziej skomplikowanego, na co nie pozwala jqLite, albo gdy trzeba użyć gotowego pluginu jQuery.

## Szybkie prototypy i małe funkcjonalności

Jeśli potrzebujemy szybko napisać jaką funkcjonalność, o wiele szybciej jest to zrobić za pomocą jQuery, szczególnie jeśli jest to coś małego. Tak jak w poprzednim pod punkcie, istnieje masa gotowych rozwiązać i szybki prototyp to może być po prostu jedna funkcja i wywołanie jednego pluginu jQuery.

Jeśli potrzebujemy jednorazowo pobrać jakieś dane ze strony, którą mamy otworzoną w przeglądarce, o wiele łatwiej jest dodać jQuery (posiadam bookmarklet do tego, czyli link w zakładkach, który wywołuje kod javascript, chociaż ostatnio rzadko z niego korzystam) i w konsoli developer tools wywołać kod jQuery. który będzie o wiele krótszy, niż natywny kod DOM. Chociaż jeśli byśmy potrzebowali napisać własny bookmarklet, pisanie go w natywnym DOM byłoby chyba lepszym rozwiązaniem, ponieważ nie musielibyśmy ładować dodatkowego pliku.

## Aplikacje rozszerzalne przez użytkowników

Jeśli byście potrzebowali napisać aplikacje/bibliotekę, która ładuje zewnętrzne pliki zdefiniowane przez użytkownika (np. pluginy), o wiele prościej dla użytkowników będzie, jeśli nie będzie trzeba tych plików kompilować, AngularJS umożliwia ładowanie jako modułów plików napisanych nawet w ES5, jeśli potrzebujemy obsługiwać przeglądarkę Internet Explorer. Z frameworkami takimi jak ReactJS czy Angular 2 jest taki problem, że podstawowy kod, nie piszę się w języku JavaScript, tylko w nakładkach, które kompilowane są do postaci kodu JavaScript. Więc jeśli umożliwiamy dodawanie pluginów, to będzie to wymagało od użytkownika dodanie `package.json` i zainstalowanie pakietów z npm. O wiele łatwiej jest napisać komponent w AngularJS, który będzie ładowany dynamicznie. Wystarczy dodać go do modułu, który jest już załadowany przez aplikacje.

Oto przykład systemu pluginów utworzony za pomocą AngularJS:

{% highlight javascript %}
// niezależny plik pluginu
angular.module('plugins').component('pluginName', {
   template: '<div><button ng-click="ctrl.check()">check</button></div>',
   controller: function() {
       this.check = function() {
          // check something
       };
   }
}).config(function(pluginRegisterService) {
    // pluginRegisterService to musi być provider aby można było użyć go w funkcji config
    pluginRegisterService.register('pluginName');
});

// i w naszej aplikacji
app.controller('main', function($scope, $element, $compile, pluginRegisterService) {
    pluginRegisterService.get().forEach(function(plugin) {
        var directive = plugin.replace(/([A-Z])/g, (_, chr) => '-' + chr.toLowerCase());
        var node = $('<' + directive + '></' + directive + '>');
        $element.find('main').append(node);
        $compile(node)($scope);
    });
});
// oprócz tego pewnie będziemy jeszcze potrzebowali jakiegoś routera aby wywoływać te pluginy np, z menu
{% endhighlight %}

Wtedy wystarczy, że pliki pluginów zostaną załadowane razem za aplikacją np. dodane do pliku html jako tagi script.

## AngularJS jest popularniejszy niż Angular2 i ReactJS

Według [ankiety z 2017 ze strony StackOverflow](https://insights.stackoverflow.com/survey/2017#technology-frameworks-libraries-and-other-technologies) AngularJS jest to najpopularniejsza technologia zaraz za Node.js.

## Podsumowanie

Osobiście sam bym się zastanawiał, czy warto uczyć się jQuery i AngularJS, zaczynając swoją przygodę z Front-Endem. Za tym, aby znać te biblioteki/frameworki, przemawia także to, że zawsze warto znać więcej niż mniej, oraz że nigdy nie wiesz do jakiego projektu możesz trafić.
