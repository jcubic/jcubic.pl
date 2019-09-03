---
layout: post
title:  "Jak posortować tablicę napisów z polskimi znakami"
date:   2019-09-03 23:22:44+0200
categories:
tags: javascript API i18n tips tablice
description: Wpis o tym jak posortować alfabetycznie tablicę z łańcuchami, które mają polskie znaki.
author: jcubic
image:
  url: "/img/pattern.jpg"
  width: 800
  height: 532
  alt: "Zdjęcie wzróru z kwadratowych przegórdek"
  attribution: źródło [maxpixel.net](https://www.maxpixel.net/Container-Pattern-Sort-Storage-Sorting-Squares-1209606), licencja CC0 Public Domain
---

Będzie to bardzo krótki wpis. Możliwe, że będzie to pierwszy z serii. Przedstawię w nim jak posortować
tablicę alfabetycznie, która zawiera ciągi z polskimi znakami (ang. array of strings).


<!-- more -->

W języku JavaScript mamy do sortowania tablic służy funkcja `sort()`, która działa tak:

{% highlight javascript %}
['Baro', 'Ąmar', 'Ęla', 'Ale'].sort();
// ["Ale", "Baro", "Ąmar", "Ęla"]
{% endhighlight %}

> NOTE: funkcja zwraca, ale też sortuje, tablicę w miejscu, więc aby nie stracić
> oryginalnej tablicy należy ją skopiować, można użyć `arr.slice().sort()`
> lub `[...arr].sort()`.

Nie jest to dokładnie to, o co nam chodzi. W poprawnie posortowanej tablicy "Ąmar"
powinno być drugie na liście (przed "Baro"). Takie posortowanie naszych ciągów znaków
wynika z faktu, że brane są pod uwagę kody znaków (ang. CodePoints), które w tablicy
znaków nie są w tych samych miejscach, co w Polskim alfabecie. Polskie znaki są na końcu,
(zobacz [tabelę Unicode](https://unicode-table.com/pl/).

A oto rozwiązanie, które poprawnie posortuje tablicę:

{% highlight javascript %}
['Baro', 'Ąmar', 'Ęla', 'Ale'].sort((a,b) => a.localeCompare(b));
// ["Ale", "Ąmar", "Baro", "Ęla"]
{% endhighlight %}

Tak samo można posortować obiekty po jakimś polu (np. po nazwisku):

{% highlight javascript %}
let arr = [{name: 'Baro'}, {name: 'Ąmar'}, {name: 'Ęla'}, {name: 'Ale'}];
arr.sort((a,b) => a.name.localeCompare(b.name));
{% endhighlight %}

Wsparcie tego API jest bardzo duże, bo 99%. Wspiera je w pełni nawet IE11
(zobacz [Can I Use](https://caniuse.com/#feat=localecompare)). Dlatego nie ma powodu, aby zawsze nie sortować tablicy
z uwzględnieniem znaków z aktualnego języka (ang. locale).

Warto też zajrzeć na stronę MDN, gdzie jest pełna dokumentacja do funkcji
[localeCompare](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/localeCompare),
która oprócz prostego wywołania, ma też masę opcji.

