---
layout: post
title:  "Alternatywa dla React/Preact + Redux"
date:   2017-08-27 17:22:10+0200
categories:
tags:  javascript react preact redux
author: jcubic
description: React lub Preact + Redux to bardzo silne połączenie, dające możliwość tworzenia skomplikowanych aplikacji typu, ale istnieje alternatywa, która zajmuje tylko 1KB
---

[React](https://facebook.github.io/react/) lub [Preact](https://preactjs.com/) + [Redux](http://redux.js.org/) to bardzo silne połączenie, dające możliwość tworzenia skomplikowanych aplikacji typu [SPA](https://en.wikipedia.org/wiki/Single-page_application), ale istnieje alternatywa, która zajmuje tylko 1KB zminimalizowana z kompresją gzip.

<!-- more -->

Jeśli musisz stworzyć małą aplikację typu SPA, react+redux lub preact+redux może być zabójcze. Biblioteki zewnętrzne będą zajmowały więcej niż cała aplikacja. Na szczęście jest alternatywa, czyli mała biblioteka [hyperapp](https://hyperapp.js.org/), która tak jak react i preact korzysta z jsx.

Przykładowa aplikacja korzystająca z tej biblioteki wygląda tak:

{% highlight jsx %}
app({
  state: {
    count: 0
  },
  view: (state, actions) =>
    <main>
      <h1>
        {state.count}
      </h1>
      <button onclick={actions.sub} disabled={state.count <= 0}>ー</button>
      <button onclick={actions.add}>＋</button>
    </main>,
  actions: {
    sub: state => ({ count: state.count - 1 }),
    add: state => ({ count: state.count + 1 })
  }
});
{% endhighlight %}

Cała aplikacja w hyperapp składa się z 3 części stanu, widoku oraz akcji:

## 1. Stan

Globalny obiekt, który jest tylko do odczytu.

## 2. Akcje

Obiekt, którego metody zmieniają stan, zwracają nowy obiekt, który jest łączony z aktualnym stanem. Nie ma potrzeby używania `Object.assign` wystarczy zwrócić część obiektu, która się zmienia. Można używać asynchronicznych akcji, ponieżej przykład takiej akcji:


{% highlight javascript %}
{
    fetch_settings: function(state, actions) {
        fetch('/settings').then((response) => response.json(); })
            .then((data) { actions.set_settings(data); });
        return {
            fetching: true;
        };
    }
}
{% endhighlight %}

Akcje mogą przyjmować jeden parametr jako trzeci argument, jeśli potrzebujemy użyć więcej parametrów możemy przekazać obiekt, możemy też skorzystać z de-strukturyzacji w ES6. np:

{% highlight javascript %}
{
    fetch_users: function(state, actions, {name, age}) {
        fetch('/user/' + name).then((response) => response.json())
            .then((data) => actions.set_user(data));
        return {
            fetching: true;
        };
    }
}
{% endhighlight %}

i wywołujemy ją w ten sposób:

{% highlight javascript %}
actions.fetch_user({name: 'Bill Murray', age: 'any'});
{% endhighlight %}

Możemy używać ES6 bo i tak będziemy musieli użyć [transpilera](https://en.wikipedia.org/wiki/Source-to-source_compiler), aby zamienić jsx na js, więc możemy przy okazji skorzystać z ES6.

## 2. Widok

Funkcja która zwraca jsx. Można tworzyć nowe komponenty (nazywane po angielsku custom tags lub widgets) jako funkcje zwracające jsx, przykładowy komponent wygląda tak:

{% highlight jsx %}
function Pagination(props, children) {
   var list = [];
   var _class;
   for (var i=0; i<props.size; i++) {
      if (i == props.selected) {
        _class = 'selected';
      } else {
        _class = '';
      }
      list.push(<li class={_class}>{i}</li>);
   }
   return (<ul>{list}</ul>);
}
{% endhighlight %}

i można go użyć tak:

{% highlight jsx %}
<Pagination size={10} selected={1}/>
{% endhighlight %}

Nazwy komponentów muszą zaczynać się wielką literą. Jeśli chcemy skorzystać, wewnątrz komponentów, z akcji lub stanu musimy niestety przekazać je jawnie jako właściwości (ang. props).

Na codepen możecie zobaczyć [przykładową aplikacje TODO](https://codepen.io/jcubic/pen/eRbjOB), napisaną przez mnie, która bazuje na [TodoMVC](http://todomvc.com/), ale nie wszystko jest zaimplementowane (brakuje przełączania widoków). Jest także oficjalna wersja TodoMVC, trochę bardziej skomplikowana, na [tej stronie](http://hyperapp-todomvc.glitch.me/). Zawiera ona wszystkie funkcje, włączając router.
