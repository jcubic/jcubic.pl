---
layout: post
title:  "Selektor CSS dla rodzica"
date:   2018-04-22 09:32:50+0200
categories:
tags:  css
author: jcubic
description: CSS3 nie udostępniał selektora rodzica, natomiast moduł Selectors Level 4 dodaje taką możliwość.
image:
  url: "/img/hands.jpg"
  alt: "Ręka niemowlaka dotyka pomarszczonej ręki dorosłego"
---

Specyfikacja CSS3 nie udostępniała żadnej możliwości zaznaczania rodzica, natomiast moduł
[Selectors Level 4](https://www.w3.org/TR/selectors-4/), który jest szkicem (ang. draft) udostępnia taką
możliwość.

<!-- more -->

Udostępnia nowa pseudo klasę `:has` podobną do `:not`, która daje możliwość pójścia dalej w głąb drzewa DOM,
ale element, który wskazuje selektor się nie zmienia np.:

{% highlight css %}
.container .wrapper:has(a.delete) {
    background-color: red;
}
.container .wrapper a.delete {
    color: red;
}
{% endhighlight %}

Powyższy kod przypisze czerwone tło dla wrappera, który ma wewnątrz siebie link o klasie delete.

Niestety [żadna przeglądarka jeszcze nie zaimplementowała pseudo klasy `:has`](https://caniuse.com/#feat=css-has).

Jest natomiast jeden selektor, który jest zaimplementowany, w niektórych przeglądarkach, a który dodaje
do CSS możliwość zaznaczania rodzica, a jest nim `:focus-within`. Działa tak jak `:has`, ale tylko dla
dziecka, które ma focus, czyli jest odpowiednikiem `:has(:focus)`.

Aby zobaczyć przeglądarki, które zaimplementowały ten selektor możesz sprawdzić:
[can I use](https://caniuse.com/#feat=css-focus-within).

O selektorze możesz przeczytać na stronie [w3.org](https://www.w3.org/TR/selectors-4/#the-focus-pseudo).

Jeśli chcesz zobaczyć jak działa, to użyłem go w tym
[Szablonie/Demo na Codepen](https://codepen.io/jcubic/pen/MbVMwO), gdzie wewnątrz elementu `.cmd`, jest
ukryty element textarea, który dostaje focus, jak się kliknie na terminal.
