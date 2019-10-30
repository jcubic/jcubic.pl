---
layout: post
title:  "JS w CSS oraz rysowanie w CSS"
date:   2019-04-19 10:20:10+0200
categories:
tags: css javascript json houdini
author: jcubic
description: Przedstawię jak można użyć JSON-a lub JavaScript wewnątrz plików CSS, aby sterować rysowaniem w CSS za pomocą JavaScript-u czyli o Houdini.
image:
 url: "/img/js-in-css.png"
 alt: "Grafika z napisem {JS} w {CSS} oraz przykładowym kodem CSS"
 width: 800
 height: 450
 attribution: Jakub T. Jankiewicz, licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/4.0/). źródło na [GitHub-ie](https://github.com/jcubic/jcubic.pl/blob/master/img/js-in-css.svg) użyto czcionki [Racing Sans One](https://www.dafontfree.io/racing-sans-one-font/)
sitemap:
  lastmod: 2019-06-23 13:17:54+0200
---

Ten wpis nie będzie o React-cie i wstawianiu CSS-a wewnątrz kodu JavaScript. Ale o czymś
zupełnie odwrotnym.  Będzie to o sposobie wstawiania kodu JS wewnątrz plików CSS. Dokładnie
chodzi o obiekty JSON, ale pewnie gdy zastosujemy pojedyncze wyrażenie bez średników to
też będzie działać. Pokaże też jak użyć części specyfikacji o nazwie Houdini do rysowania w CSS
za pomocą języka JavaScript.

<!-- more -->

## Houdini

Jest to zestaw API, dzięki którym można się podpiąć pod silnik CSS. Za ich pomocą będziemy
mogli dodawać nowe funkcjonalności do CSS, których nie ma jeszcze w przeglądarkach lub
nigdy nie będą dostępne.  Houdini to nazwa, która zawiera kilka API, z których jedne są już
zaimplementowane w niektórych przeglądarkach.  Inne są w trakcie wdrażania, a jeszcze inne
są w planach lub w trakcie definiowania specyfikacji.  Specyfikacja i implementacje mogą
się jeszcze zmienić. Na stronie [ishoudinireadyyet.com](https://ishoudinireadyyet.com/)
możesz sprawdzić status prac nad Houdini, w różnych przeglądarkach.

> **UWAGA:** z chwilą pisania tego artykułu, aby móc użyć niektórych przykładów w Google Chrome,
> trzeba włączyć opcje "Experimental Web Platform features" otwierając link:
> chrome://flags/.

## Workery

Workers czyli z angielskiego robotnicy jest to sposób dodawania nowego wątku do języka
JavaScript. Więc tak na prawdę nie jest jedno-wątkowy i można tworzyć nowe. Nowy worker
tworzy się podając scieżkę do pliku.

Mamy kilka rodzaji workerów czyli:

* Web Workers - zwykły wątek.
* Shared Workers - jest to worker do którego można się odwoływać z różnych kontekstów np. stron, iframów czy innych workerów.
* Service Workers - pisałem tym workerze we wpisie Serwer WWW w przeglądarce jest to Worker który działa po zamknięciu strony i może być np. odpowiedzialny za cachowanie stron (np. w aplikacji typu PWA) lub wysyłać powiadomienia ze strony (nawet jak zamknęliśmy stronę, a wyraziliśmy zgodę na powiadomienia).

## Data URI

Jest to sposób do definiowania kodu html z danymi jako URI (rozwijając nazwę URL dostajemy
Locator, a tutaj dane nie wskazują na lokalizacje, więc używamy URI czyli Identifier), który
zaczyna się od `data:` i może wyglądać tak:

```
data:text/html,<button>click</button><script>document.querySelector('button').addEventListener('click', () => alert('hello'));</script>
```

Dzięki temu możemy np. testować sobie kod JavaScript mając prosty edytor (czyli pasek
adresu). Zdarza mi się testować w ten sposób proste właściwości JS albo CSS.

Data URI jest ograniczone co do długości, ale można utworzyć go z obiektu Blob. Nie posiada
wtedy naszego kodu, ale ma tylko identyfikator (hash), który wskazuje na obiekt w pamięci.

Tutaj przykład funkcji blobify (nazwa może nie do końca poprawna powinno być coś
nawiązujące do URI) Bazuje ona na kodzie z tego pytania na Stack Overflow:
["How to create a Web Worker from a string"](https://stackoverflow.com/a/10372280/387194)

{% highlight javascript %}
// source: https://github.com/jcubic/favloader
function blobify(fn) {
    // ref: https://stackoverflow.com/a/10372280/387194
    var str = '(' + fn.toString() + ')()';
    var URL = window.URL || window.webkitURL;
    var blob;
    try {
        blob = new Blob([str], {type: 'application/javascript'});
    } catch (e) { // Backwards-compatibility
        window.BlobBuilder = window.BlobBuilder ||
            window.WebKitBlobBuilder ||
            window.MozBlobBuilder;
        blob = new BlobBuilder();
        blob.append(str);
        blob = blob.getBlob();
    }
    return URL.createObjectURL(blob);
}
{% endhighlight %}

Funkcja powstała do projektu [Favloader](https://github.com/jcubic/favloader) (jest to
biblioteka, która dodaje możliwość animacji favicon-ki, która nie zatrzymuje się jak
zmienimy zakładkę. Używa do tego celu workera, który trzeba było dodać w tym samym piku,
ponieważ ciężko by było użyć ścieżki, gdy używa się np. CDN).


Dalej w kodzie będziemy używać tej funkcji, aby utworzyć worker (a dokładnie paint
worklet).  Dlaczego nie użyjemy osobnego pliku? Moim zdaniem nadmierne rozdzielanie całej
aplikacji na pliki jest błędem. Ciężko cokolwiek potem znaleźć, gdy trzeba skakać między
plikami, a czasami nawet katalogami. Więc warto zależne funkcjonalności mieć w jednym
pliku. Przydaje się to też, gdy brakuje innej możliwości np. na Codpen (link na końcu).


Przykładowy URL wygląda tak:

```
"blob:https://jcubic.pl/84778d44-1597-49dd-a5f4-f759dd0fa445"
```

Więc nie jest to do końca data URI, ale działa dokładnie tak samo, tylko dane mogą mieć
nieograniczoną długość, ponieważ nie ma ich w URL-u.

> **UWAGA:** coś takiego nie zadziała z Service Workerem, który wymaga fizycznego pliku na dysku.

## Paint Worklet

Jest to rodzaj Workera, do zdań specjalnych. Tworzy się go za pomocą:

{% highlight javascript %}
CSS.paintWorklet.addModule('plik.js');
{% endhighlight %}

Wewnątrz workera mamy dostęp do API, takiego samego jak API Canvas, gdzie mamy możliwość
dowolnego rysowania. Możemy zarejestrować specjalnego Rysownika (ang. Paint), którym jest
klasa i użyć go w CSS.

{% highlight css %}
selector {
    background-image: paint(circle);
}
{% endhighlight %}

Poniżej kod prostego paint workletu, który rysuje punkt bazując na zmiennych CSS (to
jedyny sposób aby wysyłać informacje do Paint Workletu)

{% highlight jsnext %}
CSS.paintWorklet.addModule(blobify(function() {
    class Circle {

        static get inputProperties() {
            return ['--pointer-x', '--pointer-y', '--pointer-options'];
        }

        paint(context, geom, properties) {
            var x = properties.get('--pointer-x').value || 0;
            var y = properties.get('--pointer-y').value || 0;
            const prop = properties.get('--pointer-options');
            // destructure object props with defaults
            const {
                background,
                color,
                width
            } = Object.assign({
                color: 'white',
                background: 'black',
                width: 10
            }, JSON.parse(prop.toString()));
            // draw circle at point
            context.fillStyle = color;
            console.log({x,y, color, background, width})
            context.beginPath();
            context.arc(x, y, Math.floor(width / 2), 0, 2 * Math.PI, false);
            context.closePath();
            context.fill();
        }
    }
    registerPaint('circle', Circle);
}));
{% endhighlight %}

## JSON w CSS

Jak może zauważyłeś, w powyższym kodzie mamy:

{% highlight jsnext %}
const prop = properties.get('--pointer-options');
JSON.parse(prop.toString());
{% endhighlight %}

czyli parsujemy zmienną CSS (a dokładnie jej wartość) jak JSON i to działa (przynajmniej w
Chromium):

Nasz CSS wygląda tak:

{% highlight css %}
div {
    height: 100vh;
    background-image: paint(circle);
    --pointer-x: 20px;
    --pointer-y: 10px;
    --pointer-options: {
        "color": "rebeccapurple",
        "width": 20
    };
}
{% endhighlight %}

Prawdopodobnie będzie działać wszystko od dwukropka do średnika, więc pewnie można też
użyć JS i funkcji eval (nie próbowałem).

Punkty x oraz y możemy zmieniać jak ruszamy myszką, nasz worklet się uruchomi, gdy zmieni
się wartość określona jako wejście Workletu.

{% highlight javascript %}
document.querySelector('div').addEventListener('mousemove', (e) => {
    const style = e.target.style;
    style.setProperty('--pointer-x', event.clientX + 'px');
    style.setProperty('--pointer-y', event.clientY + 'px');
});
{% endhighlight %}


Dodatkowo powinniśmy jeszcze zarejestrować poszczególne typy dla zmiennych:

{% highlight javascript %}
CSS.registerProperty({
    name: '--pointer-x',
    syntax: '<length>',
    inherits: false,
    initialValue: '10px'
});
CSS.registerProperty({
    name: '--pointer-y',
    syntax: '<length>',
    inherits: false,
    initialValue: '10px'
});
CSS.registerProperty({
    name: '--pointer-options',
    inherits: false,
    initialValue: '{}'
});
{% endhighlight %}

[Tutaj link do demo na CodePen](https://codepen.io/jcubic/pen/JwVXKe).


*[CDN]: Content Delivery Network
*[PWA]: Progressive Web Apps
