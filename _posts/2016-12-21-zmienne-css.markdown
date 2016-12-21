---
layout: post
title:  "Zmienne CSS"
date:   2016-12-21 20:27:43+01:00
categories: 
tags:  css css3 properties javascript
author: jcubic
---

Zmienne CSS, a dokładniej customowe właściwości (ang. custom properties) są [nowym modułem standardu CSS](https://www.w3.org/TR/css-variables/),
w tym poście przedstawię jak ich używać. 

<!-- more -->

Zmienne CSS zapisujemy używając dwóch myślników prze nazwą np.:

{% highlight css %}
p {
    --color: red;
}
{% endhighlight %}

Możecie się zastanawiać, dlaczego nie `$color`. Składnie tą użyto, ponieważ dolara używają preprocesory CSS takie jak
[Less](https://pl.wikipedia.org/wiki/Less_(j%C4%99zyk_arkuszy_styl%C3%B3w)) czy [Sass](https://en.wikipedia.org/wiki/Sass_(stylesheet_language))
więc skorzystano z innej składni, aby było możliwe użycie zmiennych Less/Sass w tym samym arkuszu razem ze zmiennymi css.

Aby użyć zmiennej CSS, korzystamy z funkcji var np.:

{% highlight css %}
p {
    background: var(--color);
}
{% endhighlight %}

Możemy wykryć czy zmienne css są obsługiwane przez przeglądarkę za pomocą [reguły at](https://developer.mozilla.org/en-US/docs/Web/CSS/At-rule) `@support`,
aczkolwiek `@support` nie jest obsługiwany przez przeglądarkę Internet Exporer, ale nie obsługuje ona także zmiennych CSS, więc to nie ma znaczenia.

{% highlight css %}
article {
    background: red;
}

@supports (--css: variables) {
    article {
        background: green;
    }
}
{% endhighlight %}

Powyższy kod ustawi tło jako kolor czerwony, jeśli przeglądarka nie obsługuje zmiennych css
lub zielony, jeśli je obsługuje.

Jeśli mamy kod, który używa tej samej właściwości dwa razy np.:

{% highlight css %}
div.box {
    font-size: 45px;
    width: 6em;
    height: 6em;
}
{% endhighlight %}

Możemy skrócić ten kod używając zmiennych np.:

{% highlight css %}
div.box {
    font-size: 45px;
    --size: 6em;
    width: var(--size);
    height: var(--size);
}
{% endhighlight %}

Aby zmienna `--size` miała wartość `6`, nie można użyć `width: var(--size)em`, ponieważ przeglądarka zinterpretuje to jako `6 em`,
na szczęście można skonwertować wartość liczbową do jednostkowej używając `calc` i mnożąc przez 1em np.:

{% highlight css %}
div.box {
    font-size: 45px;
    --size: 6;
    width: calc(var(--size) * 1em);
    height: calc(var(--size) * 1em);
}
{% endhighlight %}

lub

{% highlight css %}
div.box {
    font-size: 45px;
    --side: 6;
    --size: calc(var(--size) * 1em);
    width: var(--size);
    height: var(--size);
}
{% endhighlight %}

Zmiennych można także używać jako style inline np.:

{% highlight html %}
<div class="box" style="--side: 7"></div>
{% endhighlight %}


Zmiennych CSS można użyć, aby skrócić zapis nowych włąściwości z prefixami np:

{% highlight css %}
* {
    --box-shadow: initial;
    -webkit-box-shadow: var(--box-shadow);
    -moz-box-shadow: var(--box-shadow);
    box-shadow: var(--box-shadow);
}
{% endhighlight %}

i użyć tej zmiennej na jakimś elemencie np.:

{% highlight css %}
.box {
    --box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}
{% endhighlight %}

Można ich używać, aby dodać nowe właściwości np `--prepend`, która doda nowy tekst przed elementem np.:

{% highlight css %}
* {
    --prepend: initial;
}
::before {
    content: var(--prepend);
}
{% endhighlight %}

i wtedy możemy dodać tekst, korzystając z poniższego kodu:

{% highlight css %}
.box {
    --prepend: "hello";
}
{% endhighlight %}

może nawet używać tej właściwości inline, np.:

{% highlight html %}
<div style="--prepend: 'hello '">world!</div>
{% endhighlight %}

Można definiować zmienne w JavaScript. Aby pobrać wartość można użyć:

{% highlight javascript %}
element.style.getPropertyValue("--foo");
{% endhighlight %}

powyższy kod pobierze wartość zmiennej inline, aby pobrać wartość, która może być zdefiniowana w pliku css, można użyć:

{% highlight javascript %}
getComputedStyle(element).getPropertyValue("--foo");
{% endhighlight %}

aby przypisać wartość, korzystamy z funkcji `setProperty` np.:

{% highlight javascript %}
element.style.setProperty("--foo", 10);
{% endhighlight %}

Możemy przypisać je do elementu root, aby potem użyć dla dowolnego elementu:

{% highlight javascript %}
var root = document.documentElement;
root.style.setProperty("--foo", 10);
{% endhighlight %}

Można ich użyć aby dodać wartość elementu input i użyć go w css:

{% highlight javascript %}
function value(input) {
    input.style.setProperty('--value', input.value);
}
for (var input of document.querySelectorAll('input')) {
    value(input);
}
document.addEventListener('input', function(event) {
    value(event.target);
});
{% endhighlight %}

po dodaniu zmiennej `--value`, można jej użyć dla elementu input o typie `range`, aby dodać inne tło od początku do uchwytu:

{% highlight css %}
input[type="range"] {
    appearance: none;
    background: linear-gradient(to right, red calc(var(--value) * 1%), transparent 0);
}
{% endhighlight %}

Możecie się zastanawiać czy można używać zmiennych css już dzisiaj. Okazuje się, że zmienne css są dostępne w większości
nowoczesnych przeglądarek oprócz Internet Exportera i Edge, chociaż wiadomo (w chwili pisania tego artykułu), że ich obsługa ma zostać dodana w nowej wersji 15 przeglądarki Edge.
Możecie zobaczyć jakie przeglądarki już je zaimplementowały na stronie [can I use](http://caniuse.com/#feat=css-variables).