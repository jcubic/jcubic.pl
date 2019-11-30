---
layout: post
title:  "15 Pytań na rozmowę rekrutacyjną z HTML5"
date:   2019-11-30 13:11:01+0100
categories:
tags: html praca
author: jcubic
description: Tym razem 15 pytań na rozmowę kwalifikacyjną z HTML5
image:
 url: "/img/html-code-screen.jpg"
 alt: "Zdjecie przedstawiające kod HTML na ekranie komputera"
 width: 800
 height: 449
related:
  -
    name: "5 Pytań na rozmowę rekrutacyjną z JavaScript"
    url: "/2017/09/pytania-na-rozmowe-rekrutacyjna-javascript.html"
  -
    name: "Kolejne 10 pytań na rozmowę rekrutacyjną z języka JavaScript"
    url: "/2019/03/kolejne-pytania-na-rozmowe-rekrutacyjna-javascript.html"
  -
    name: "15 Pytań na rozmowę rekrutacyjną z CSS"
    url: "/2018/10/pytania-rekrutacyjne-css.html"
---

Były pytania z [CSS](/2018/10/pytania-rekrutacyjne-css.html) oraz
[JavaScript](/2017/09/pytania-na-rozmowe-rekrutacyjna-javascript.html) i
[druga część](/2019/03/kolejne-pytania-na-rozmowe-rekrutacyjna-javascript.html). Aby jednak mieć
komplet pytań technologicznych na Front-End developera, teraz pora na pytania rekrutacyjne z HTML, a
dokładnie chodzi o pytania z HTML5.

Wydaje mi się, że raczej pytania na rozmowach rekrutacyjnych będą dotyczyły języków JavaScript oraz
CSS. Tego typu pytania zadałbym jednak pewnie na rozmowie rekrutacyjnej na web mastera lub web
designera. Więc może się komuś przydadzą.

W pytaniach zamieściłem dwie zagadki z kodem HTML. Są to ciekawostki na pograniczu HTML oraz
JavaScript. Gdy rekrutujesz, na stanowisko programisty JavaScript, możesz zadać jedną z nich.

<!-- more -->

## 1. Czym różni się znacznik `header` od `h1`?

Header jest to jeden ze znaczników semantycznych, za jego pomocą możemy utworzyć nagłówek.
Który np. ma w sobie h1 a pod sobą np. imię, nazwisko i datę. Można też w nim umieścić.
Nawigacje. Natomiast h1 jak i inne znaczniki h1-h6 służą do tworzenia nagłówka do jakiegoś
tekstu np. tytuł artykułu, lub sekcji.

## 2. Do czego służy znacznik main?

Według [MDN](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/main) jest to znacznik,
który służy do umieszczenia głównej części strony. Powinien być tylko jeden taki znacznik
na stronie (chociaż były o to spory to miedzy dwoma specyfikacjami, niestety nie mam
żadnego źródła, jeśli znasz dodaj w komentarzu).

## 3. Wymień 3 nowe tagi w HTML5:

oto kilka: section, article, header, footer, main, video, audio, strong

## 4. Jak jest różnica między `div` a `span`?

`div` jest to element blokowy, którego szerokość wynosi 100% natomiast tag `span` jest to element
inline który można np. wstawić wewnątrz tekstu.  Będzie się on zachować jak tekst, czyli można
np. zapisać:

{% highlight html %}
foo <span>bar</span> baz
{% endhighlight %}

Będzie to jedna linia, tak jakby elementu span nie było, można go jednak ostylować inaczej nadając
mu np. kolor za pomocą CSS.

## 5. Wymień 5 technologii występujących w HTML5

Canvas, WebGL, History API, Storage, Drag & Drop, content editable,
Wysyłanie wiadomości (czyli postMessage i zdarzenie message), tagi audio i video.

API które nie wchodzą do specyfikacji HTML5 to m.in. GEO Lokalizacja, WebRTC czy obsługa plików
(File API, Directory API).

## 6. Jak zapisać kodowanie znaków i jakie najczęściej się stosuje?

Najczęściej stosuje się [UTF-8](https://pl.wikipedia.org/wiki/UTF-8), czyli kodowanie znaków
[Unicode](https://pl.wikipedia.org/wiki/Unikod). Na stronie wystarczy dodać:

{% highlight html %}
<meta charset="utf8"/>
{% endhighlight %}

w sekcji `head`. Jest to pozostałość po takim długim wpisie ze starego HTML4.

{% highlight html %}
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
{% endhighlight %}

Który nadal działa (jest to odpowiednik nagłówka HTTP na stronie HTML). Przeglądarki po prostu
rozpoznawały samą końcówkę `charset=utf-8`, więc wystarczyło wstawić je jako tag w html5.

## 7. Jak należy wskazać przeglądarce, że strona jest zapisana w HTML5?

Strona powinna mieć na początku typ dokumentu. Kiedyś był bardzo długi i zawsze trzeba było szukać,
jak go zapisać, albo mieć zapisane szablony plików HTML. Oto przykład:

{% highlight html %}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
{% endhighlight %}

Dzisiaj wystarczy zapisać:


{% highlight html %}
<!DOCTYPE html>
{% endhighlight %}

I to wystarczy. Warto zawsze dodawać, ponieważ gdy go nie ma, strona może się dziwnie renderować.
Jest to spowodowane tym że może przejść w tzw.
[quirks mode](https://developer.mozilla.org/en-US/docs/Web/HTML/Quirks_Mode_and_Standards_Mode).

## 8. Czym się różni `ol` od `ul`?

* ol - czyli ordered list to lista z numerami
* ul - unordered list - lista puntów

## 9. [Zagadka] Co się stanie, gdy kliknie się ten przycisk?

Masz taki html:

{% highlight html %}
<form>
  <button onclick="x()">click</button>
</form>
{% endhighlight %}

Zakładając, że nie ma żadnego pliku JavaScript, co się stanie, gdy klikniesz przycisk i dlaczego?

Odpowiedź zostanie wysłane zapytanie HTTP do tej samej strony ponieważ, przycisk button, jeśli nie ma
`type="button"`, działa tak jak `<input type="submit"/>`. Przez ułamek sekundy pojawi się błąd w
konsoli, ale zniknie, jeśli nie ma włączonej specjalnej opcji (chodzi o preserve logs). Gdy nie mamy
konsoli deweloperskiej, jedyną zmianą będzie znak `"?"` na końcu URL. Gdy jest to plik html o nazwie
`foo.html` adres zmieni się na `foo.html?` (jest to spowodowane tym, że wysyłany jest Query String,
tylko pusty, ponieważ nie ma żadnych parametrów)

## 10. Do czego służy atrybut `role`?

Jest to znacznik, który określa znaczenie elementu. Głównie do celów dostępności
(ang. accessibility). Jest częścią specyfikacji ARIA. Został też dodany do HTML5.

## 11. Jak dodać swój własny atrybut, aby był zgodny z HTML5?

Do html można dodawać dowolne atrybuty, przeglądarki nie będą miały z nimi problemu. Ale aby być
jednak zgodnym z HTML5, należy użyć atrybutu `data-nazwa="wartość"`. Taki atrybut można potem pobrać
za pomocą `element.dataset.nazwa`.  (Zadziała też starszy, działający w każdej przeglądarce,
`getAttribute`).

## 12. [Zagadka] Co się stanie, gdy kliknie się ten przycisk?

{% highlight html %}
<button id="foo" onclick="foo.remove()">click</button>
{% endhighlight %}

Przycisk zostanie usunięty, ponieważ każdy element, który ma atrybut `id` w html, zostanie dodany w
JavaScript jako zmienna globalna. Funkcja/metoda `remove` to nowe API, które działa jak starsze
`foo.parentNode.removeChild(foo)`.

## 13. Jak utworzyć link do elementu tej samej strony?

Za pomocą atrybutu `id` oraz linku (kotwicy).

{% highlight html %}
<a href="#foo">p</p>
<p id="foo">Jakiś Text</p>
{% endhighlight %}

## 14. Jak ograniczyć liczbę znaków w polu tekstowym?

Służy do tego atrybut `maxlength`. wystarczy np. ustawić `maxlength="100"`.

## 15. Jak dodać walidacje za pomocą samego HTML?

{% highlight html %}
<form>
  <input required pattern="x{2}y{2}"/>
</form>
{% endhighlight %}

w powyższym formularzu mamy walidację, która wymaga aby pole input, było wypełnione, oraz aby
wartość w tym polu równała się `xxyy` za pomocą wyrażenia regularnego.

Więcej o walidacji w artykule:
["Natywna walidacja formularzy HTML5"](https://www.nafrontendzie.pl/natywna-walidacja-formularzy-html5).

*[HTML]: HyperText Markup Language
*[HTTP]: Hypertext Transfer Protocol
*[WebRTC]: Web Real-Time Communication
