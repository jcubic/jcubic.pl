---
layout: post
title:  "15 Pytań na rozmowę rekrutacyjną z CSS"
date:   2018-10-20 17:47:01+0200
categories:
tags: css praca
author: jcubic
description: Tym razem 15 pytań z CSS na rozmowie kwalifikacyjnej na stanowisko Front-End Developer. CSS to po JavaScript najważniejsza część Front-Endu.
image:
 url: "/img/css-interview.jpg"
 alt: "Grafika wektorowa przedstawiająca osobę z laptopem i tekst CSS w klamerkach"
 width: 800
 height: 464
related:
  -
    name: "5 Pytań na rozmowę rekrutacyjną z JavaScript"
    url: "/2017/09/pytania-na-rozmowe-rekrutacyjna-javascript.html"
sitemap:
  lastmod: 2018-10-22 09:20:04+0200
---

Były już [pytania rekrutacyjne z JavaScript](/2017/09/pytania-na-rozmowe-rekrutacyjna-javascript.html) oraz
[pytania z React.js](/2018/10/pytania-rekrutacyjne-z-react.js.html), tym razem 15 pytań, jakie bym zadał na rozmowie
rekrutacyjnej z CSS.  Takie pytania mogłyby się pojawić na rozmowie kwalifikacyjnej na stanowisko Front-End
developer. Na pewno by były któreś z tych, gdybym ja rekrutował albo weryfikował kandydata.

<!-- more -->

Na stanowisko Front-End developer mogą się pojawić też pytania z HTML (ale raczej z HTML5 np. jakieś nowe API), ale
Front-End to głównie CSS i JavaScript. A oto lista pytań:

## 1. Czy ten kod wypełni całą stronę na czerwono?

{% highlight html %}
<!DOCTYPE html>
<html>
<body>
  <div style="height:100%;background:red;"></div>
</body>
</html>
{% endhighlight %}

Nie, ponieważ body ma `padding 10px`, więc będzie biała ramka, ale to i tak nie ma znaczenie, ponieważ wysokość body
zawiera tylko kontent, dlatego jego wysokość będzie równa 0, div też będzie miał wysokość 0. W CSS wysokość 100% jest
zawsze w odniesieniu do rodzica.  (jeśli musimy ustawić wysokość w procentach, nigdzie w drzewie rodziców nie może być
`height: auto`, a taka wartość jest na body).

Aby rozwiązać te problemy należy ustawić:

{% highlight css %}
body,html {
  height: 100%;
}
body {
  margin: 0;
}
{% endhighlight %}

Co ciekawe jeśli nie użyjemy `<!DOCTYPE html>` czyli html5, to cała strona będzie czerwona, oprócz marginesu na body
(przynajmniej w Google Chrome).

## 2. Jak wycentrować element w pionie i w poziomie

* Można użyć wyświetlania tabelkowego, w przypadku którego działa `vertical-align`:

{% highlight css %}
.container {
    display: table;
    text-align: center;
}
.container .item {
    display: table-cell;
    vertical-align: middle;
}
.container .item div {
    background: blue;
    display: inline;
}
{% endhighlight %}

* Można użyć `position: absolute` + `transform`:

{% highlight css %}
.container {
    position: relative;
}
.container .item {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}
{% endhighlight %}

`left` i `top` `50%` wyrówna lewą i górną krawędź elementu `.item` do środka a `transform: translate` wyrówna do środka
względem samego elementu.  Zadziała to ponieważ dla pozycji 50% jest w stosunku do kontenera natomiast 50% dla `transform`
jest w stosunku do aktualnego elementu.

* Inne rozwiązanie to nowy [flexbox](https://en.wikipedia.org/wiki/CSS_flex-box_layout):

{% highlight css %}
.container {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.container .item {
    width: 10em;
}
{% endhighlight %}

* Można też użyć:

{% highlight css %}
.container {
   margin: 0 auto;
   width: 100px;
}
{% endhighlight %}

ale to rozwiązanie zadziała tylko dla wyrównania w poziomie, `margin: auto;` nie wyrówna elementu w pionie.

## 3. Który selektor jest silniejszy

* `#content .item ul li[data-id]`
* `#content .container .item li.selected`

Siła selektora określa się za pomocą kilku liczb, które określają:

1. liczbę selektorów identyfikatorów czyli `#foo`,
2. liczbę klas, selektorów atrybutów np. `[data-id]` lub `[data-id^="1"]` oraz pseudo klas, takich jak `:hover`,
3. liczbę elementów oraz pseudo elementów, takich jak `::before` oraz `::after`, które działają także z jednym dwukropkiem.

Siły obu selektorów to odpowiednio 1 2 2 oraz 1 3 1, dlatego drugi selektor ma większą siłę ponieważ ma 3 na drugiej
pozycji (1 na trzeciej nie ma już znaczenia). Jeśli oba selektory odwołują się do tego samego elementu, pobrane zostaną
właściwości z tego drugiego oraz te z pierwszego, których nie ma w tym drugim.

{% highlight css %}
.#content .item ul li[data-id] {
   color: red;
   background: white;
}
#content .container .item li.selected {
   background: gray;
}
{% endhighlight %}

Zakładając, że wszystkie elementy `li` mają atrybut `data-id`, oraz że nie ma innych reguł dla elementów li. Element
`li.selected`, będzie miał kolor czerwony oraz tło szare, natomiast pozostałe elementy `li`, będą miały kolor czerwony i
białe tło.

Dodatkowo są jeszcze style inline (za pomocą atrybutu style), które nadpsują właściwości z każdego selektora oraz słowo
kluczowe `!important` (dodaje się je na końcu właściwości np. `float: left !important;`), które nadpisuje wszystkie
właściwości włączając style inline.

## 4. Wymień 4 selektory, które zaznaczają element z konkretnym indeksem

* `:nth-child(1)` - wybiera kolejny element
* :nth-last-child(1)` - kolejny element od końca
* `:nth-of-type(1)` - kolejny element danego typu np. `div:nth-of-type(1)` zaznaczy pierwszy `div`, nawet jeśli jest przed
 nim `header`
* `:nth-last-of-type(1)` - to samo co poprzednie, ale od końca

Oprócz indeksu liczbowego, można także wstawiać literę `n`, za pomocą której można opisać proste równania, np, `3n`
zaznaczy co 3 element.

## 5. Jak wybrać pierwsze 10 elementów na liście

Należy użyć selektora `nth-child` oraz równania `-n+10`

{% highlight css %}
ul li:nth-child(-n+10) {
   background: red;
}
{% endhighlight %}

`-n` oznacza wszystkie elementy przed pierwszym, a `+10` przesuwa cały zakres o 10 elementów do przodu

## 6. Jak zaznaczyć parzyste elementy na liście

{% highlight css %}
ul li:nth-child(even) {
    background: red;
}
/* lub */
ul li:nth-child(2n) {
    background: red;
}
{% endhighlight %}

Dla nieparzystych trzeba użyć `odd` lub `2n-1`.

## 7. Jak sprawdzić czy zmienne css (ang. custom properties) lub dowolna inna właściwość jest obsługiwana przez przeglądarkę

Można użyć reguły `@support` (działa w każdej przeglądarce oprócz IE, która zignoruje cały kod wewnątrz, nie działają
tam także zmienne CSS)

{% highlight css %}
@supports (--css: variables) {
    .item {
       color: var(--color, white);
    }
}
{% endhighlight %}

Nie ma znaczenia jaką zmienną i jaką wartość podamy może być np. `@supports (--color: red)`

## 8. Jak używać zmiennych CSS z liczbowymi wartościami

Najlepiej przypisać do nich wartości bez jednostki i użyć calc do skonwertowania ich na daną jednostkę.
Robi się tak ponieważ konwersja w drugą stronę jest niemożliwa:

{% highlight css %}
.item.selected {
   --width: 200;
}
.item {
    width: calc(var(--width, 100) * 1px);
}
{% endhighlight %}

Jako bonus można wspomnieć, że nie można animować zmiennych CSS, ale niedługo dostępne będzie nowe API, które jest
częścią specyfikacji [Houdini](https://ishoudinireadyyet.com/) (a dokładnie Properties & Values API), dzięki któremu
będzie można animować zmienne CSS. Realizowane będzie to w ten sposób, że zmienną CSS będzie można zarejestrować i podać
jej typ za pomocą funkcji `CSS.registerProperty`. Funkcja jest już dostępna w Google Chrome, ale działa na razie głównie
z Paint API (w uproszeniu jest to API, które daje możliwość obsługi właściwości `background-image`, poprzez takie samo
API jak to od elementu `canvas`).

Tutaj [moje proste demko Paint API](https://codepen.io/jcubic/pen/KBQxjO) (według podlinkowanej strony powinno działać
w przeglądarkach Google Chrome/Chromium oraz Opera).

## 9. Jak ustawić i pobrać zmienną CSS z poziomu JavaScript-u

Aby pobrać wartość, która jest ustawiona w atrybucie style, wystarczy

{% highlight javascript %}
element.style.getPropertyValue('--zmienna');
{% endhighlight %}

Aby pobrać wartość, która ustawiona jest w arkuszu lub zagnieżdżona w tagu style, należy użyć tego kodu:

{% highlight javascript %}
var style = getComputedStyle(element);
style.getPropertyValue('--zmienna');
{% endhighlight %}

Aby zapisać wartość do zmiennej trzeba użyć:

{% highlight javascript %}
var zmienna = element.style.setProperty('--zmienna', 'wartość');
{% endhighlight %}

## 10. Jak utworzyć selektor atrybutu, który nie jest wrażliwy na wielkość liter

W CSS selektor atrybutu, czyli `li[data-id="foo"]` jest wrażliwy na wielkość liter, dlatego gdy mamy element `<li
data-id="Foo">`, oraz selektor `[data-id="foo"]` to nie będzie on dopasowany do tego elementu. Aby zaznaczyć element za
pomocą selektora atrybutu, bez rozróżniania wielkości liter, należy zastosować taki selektor:

{% highlight css %}
li[data-id="foo" i] {
    color: blue;
}
{% endhighlight %}

## 11. Jak utworzyć sprite w CSS

Należy utworzyć obrazek, gdzie wszystkie mniejsze obrazki będą zamieszczone obok siebie w pionie lub poziomie lub w obu
osiach. Następnie należy ustawić `background: url(sprite.png) no-repeat;` i dla każdego elementu, który powinien mieć
inną ikonę obrazek zmieniać `background-position`.

{% highlight css %}
.icon {
    background: url(sprite.png) no-repeat;
    width: 64px;
    height: 64px;
}
.icon.smile {
    background-position: -64px 0;
}
.icon.thumb-up {
    background-position: -128px 0;
}
.icon.ball {
    background-position: -192px 0;
}
{% endhighlight %}

W powyższym przykładzie będziemy mieli 4 obrazki w spirte-cie uszeregowane w poziomie co 64 piksele. Pierwszy obrazek
nie musi mieć `background-position` ponieważ będzie on równy `0 0`, drugim będzie smile, trzecim thumb-up a czwartym
ball.

## 12. Zadanie: zamień tekst linku na obrazek

{% highlight html %}
<a href="/" class="logo">Company</a>
{% endhighlight %}

Odpowiedź:

{% highlight css %}
.logo {
    background: url(logo.png);
    width: 100px;
    height: 50px;
    text-indent: -99999em;
}
{% endhighlight %}

## 13. Jak działa model pudełkowy (ang. box model)

![Model Pudełkowy CSS](/img/Css_box_model.svg)
<small>Źródło: [Wikimedia commons](https://commons.wikimedia.org/wiki/File:Css_box_model.svg) autor
[Felix.leg](https://commons.wikimedia.org/wiki/User:Felix.leg) Licencja
[CSS-BY-SA](https://creativecommons.org/licenses/by-sa/3.0/deed.en)
</small>

Każdy element składa się z pudełka według standardu W3C, który nie jest zaimplementowany poprawnie w starszej wersji IE.
Gdy ustawia się `width` albo `height` to zmieniana jest szerokość i wysokość zawartości, do której dodawane są `padding`,
`border` oraz `margin`.  Dlatego gdy jakiekolwiek z tych wartości jest podana to szerokość i/lub wysokość pudełka będzie
większa niż ustawione `width` i/lub `height`. Można to skorygować ustawiając szerokość na `calc(100% - padding - border)`.

Można zmienić to zachowanie za pomocą właściwości `box-sizing`, które przyjmuje wartości `content-box` (domyślana) oraz
`border-box`, która spowoduje, że `width` oraz `height` będzie zawierała całe pudełko wliczając padding i border co może
być bardziej intuicyjne. (według [Can I use](https://caniuse.com/#feat=css3-boxsizing) box-sizing jest zaimplementowany
we wszystkich przeglądarkach, wliczając IE8).

I na koniec jest jeszcze jedna właściwość, która nie jest częścią modelu pudełkowego, jest nią `outline`. Działą tak jak
`border`, ale ramka jest rysowana na zewnątrz border, nie zmienia się też szerokości oraz wysokość pudełka (`outline`
jest niezależne).

## 14. Jak przyspieszyć animacje i przejścia (ang. transitions) w CSS

Jeśli jest taka możliwość, należy animować tylko właściwości `transform` oraz `opacity`, które są mało zasobożerne jeśli
chodzi o animacje.  Użycie `transform: translate`, do przesuwania elementu na ekranie, będzie bardziej płynne od zmiany
właściwości `left` lub `top`.  Dzieje się tak ponieważ, przy zmianie `opacity` oraz `transform`, nie jest wykonywana
operacja layoutu, czyli obliczania wszystkich stylów na stronie i aplikowania ich do poszczególnych elementów.

## 15. Jak ustawić wysokość lub szerokość na 100% w odniesieniu do całego okna przeglądarki

Stary sposób polegałby na ustawieniu `body, html` na `100%` oraz każdego kontenera, aż do elementu, którego wysokość lub
szerokości musimy ustawić.  Inny sposób jest to użycie nowych jednostek `vh` oraz `vw`; które działają jak procent, ale w
odniesieniu do całego okna przeglądarki.

{% highlight css %}
.element {
    width: 100vw;
    height: 100vh;
}
{% endhighlight %}

Istnieją także jednostki `vmax` oraz `vmin`, które oznaczają większą lub mniejszą z dwóch wartości, czyli `vmax ==
max(width, height)` oraz `vmin == min(width, height)`.

## Bonus

Warto także znać [Flexboxa](https://en.wikipedia.org/wiki/CSS_flex-box_layout) oraz
[CSS Grid](https://en.wikipedia.org/wiki/CSS_grid_layout), ale jeśli chodzi o mnie, to raczej bym o nie nie pytał, może
tylko czy kandydat je zna i czym się różnią.  A odpowiedź na to pytanie brzmi:
[Flexbox](https://en.wikipedia.org/wiki/CSS_flex-box_layout) tworzy layout w jednym wymiarze, w kolumnie lub wierszu,
natomiast [CSS Grid](https://en.wikipedia.org/wiki/CSS_grid_layout) tworzy layout w dwóch wymiarach.

Referencje:
* [CSS Specificity: Things You Should Know](https://www.smashingmagazine.com/2007/07/css-specificity-things-you-should-know/)
* [High Performance Animations](https://www.html5rocks.com/en/tutorials/speed/high-performance-animations/)
* [Can I Use CSS Feature Queries](https://caniuse.com/#feat=css-featurequeries)
* [Can I Use Viewport units: vw, vh, vmin, vmax](https://caniuse.com/#feat=viewport-units)

*[CSS]: Cascading Style Sheets
*[W3C]: World Wide Web Consortium
*[API]: Application Programming Interface
*[ball]: ang. piłka
