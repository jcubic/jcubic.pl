---
layout: post
title:  "Zmiana styli CSS gdy JavaScript jest włączony lub nie w samym CSS"
date:   2018-12-20 17:33:29+0100
categories:
tags: css javascript
author: jcubic
description: Jak w samym CSS stworzyć włącznik elementów w zależności gdy JavaScript jest włączony lub nie. Proste rozwiązanie bez pomocy JavaScript.
image:
 url: "/img/light-switch.jpg"
 alt: "Włącznik Światła z ikonką żarówki"
 width: 800
 height: 603
 attribution: "Autor [@joffi](https://pixabay.com/pl/users/joffi-1229850/) źródło [pixabay.com](https://pixabay.com/pl/w%C5%82%C4%85cznik-%C5%9Bwiat%C5%82a-%C5%9Bwiat%C5%82o-prze%C5%82%C4%85cznik-1519735/) licencja [CC0](https://creativecommons.org/publicdomain/zero/1.0/deed.pl)"
---

Gdy mamy layout i chcemy, aby pojawił się baner informujący, że aplikacja wymaga JavaScript'u, najczęstszym rozwiązaniem
jest dodanie klasy do taga html np. `no-js` i usunięcie jej w JavaScripcie.

Jest jednak sposób, aby to zrobić, w samym CSS.

<!-- more -->

Oto bardzo sprytne rozwiązanie, którego autorem jest [Stas Lashmanov](https://twitter.com/CyberAP/status/1075431430958800896):

{% highlight html %}
<style>
  .js-only {
    display: var(--no-js-hide, block);
  }
</style>

<noscript>
  <style>
    :root {
      --no-js-hide: none;
    }
  </style>
</noscript>
{% endhighlight %}

Rozwiązanie nie zadziała jednak w przeglądarce Internet Explorer, która nie
[wspiera zmiennych CSS](https://caniuse.com/#feat=css-variables).
