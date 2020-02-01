---
layout: post
title:  "15 Pytań na rozmowę rekrutacyjną z React.js"
date:   2018-10-04 18:26:30+0200
categories:
tags: javascript praca react
author: jcubic
description: Tym razem 15 pytań na rozmowę kwalifikacyjną z React.js
image:
 url: "/img/reactjs-interview.jpg"
 alt: "Grafika wektorowa przedstawiająca osobę z laptopem i logo React.js"
 width: 800
 height: 464
related:
  -
    name: "5 Pytań na rozmowę rekrutacyjną z JavaScript"
    url: "/2017/09/pytania-na-rozmowe-rekrutacyjna-javascript.html"
  -
    name: "Kolejne 10 pytań na rozmowę rekrutacyjną z języka JavaScript"
    url: "/2019/03/kolejne-pytania-na-rozmowe-rekrutacyjna-javascript.html"
sitemap:
  lastmod: 2020-02-01 20:23:48+0100
---

React.js jest to bardzo popularny framework JavaScript, stworzony przez jednego z programistów
Facebook'a, dalej rozwijany jako projekt Open Source, gdzie głównymi osobami rozwijającymi projekt,
są programiści Facebook'a. W tym wpisie przedstawię 15 pytań, które uważam, mogłyby się pojawić na
rozmowie kwalifikacyjnej na programistę Front-End, tym razem z React.js. Gdybym musiał rekrutować,
to pewnie bym zadał któreś z tych pytań albo wszystkie. Koniecznie przeczytaj przed rozmową rekrutacyjną.
Jeśli jesteś rekruterem warto też tutaj zajrzeć.

<!-- more -->

## 1. Podaj sposoby definiowania komponentów w React

* Jako klasa ES6:

{% highlight jsx %}
class List extends React.Component {
    constructor(props) {
        super(props);
        this.sate = {label: 'React'};
    }
    render() {
        return <ul>{
              this.props.items.map(function(item) {
                  return <li>{item.name}</li>;
              })
        }</ul>
    }
}
{% endhighlight %}

* Jako komponent bez stanu (ang. state) za pomocą funkcji.

{% highlight jsx %}
function List(props) {
    return <ul>{
            props.items.map(function(item) {
                return <li>{item.name}</li>;
            })
        }</ul>
}
{% endhighlight %}

Trzeba jednak dodać że od wersji React 16.8 mogą mieć stan dzięki Hakom (ang. Hooks).  Tutaj chodzi
o stan komponentu, który zapisuje się za pomocą funkcji `setState`.

* składnia ES5 za pomocą pakietu npm `create-react-class`:

{% highlight jsx %}
var createReactClass = require('create-react-class');
var List = createReactClass({

    displayName: 'List',

    getInitialState: function() {
        return {items: []}
    },

    getDefaultProps: function(){
        return {items: []}
    },

    _handleClick: function(){
        alert('hello world!')
    },

    render: function(){
        return <ul>{
              this.props.items.map(function(item) {
                  return <li>{item.name}</li>;
              })
        }</ul>
    }
});
{% endhighlight %}

Kiedyś była możliwość skorzystania z funkcji `React.createClass`, ale została usunięta na rzecz osobnego modułu npm.

Wszystkich trzech komponentów można użyć w ten sposób:

{% highlight jsx %}
ReactDOM.render(
  <List items={[{name: "foo"}, {name: "bar"}]}/>,
  document.getElementById('root')
);
{% endhighlight %}

Co ciekawe istnieje możliwość napisania aplikacji w ES5 bez JSX i kompilacji.

## 2. Co to są komponenty wyższego rzędu

Komponenty wyższego rzędu (ang. Higher Order Component - HOC), zasada jest podobna do funkcji
wyższego rzędu. Komponenty tego typu do funkcje, które przyjmują komponenty jako argument oraz
zwracają nowy argument.

Przykładem może być funkcja connect z biblioteki Redux.

## 3. Co to są render props

Render props są to właściwości komponentu, które zawierają funkcje, która otrzymuje stan komponentu lub jakieś inne dane
i zwraca kod jsx:


{% highlight jsx %}
class Toggler extends React.Component {
   constructor(...args) {
       super(...args)
       this.state = {enabled: false};
       this.toggle = this.toggle.bind(this);
   }
   toggle() {
       this.setState({enabled: !this.state.enabled});
   }
   render() {
      return <div onClick={this.toggle}>{this.props.render(this.state)}</div>
   }
}
ReactDOM.render(
  <Toggler render={({enabled}) => enabled ? <div>ON</div> : <div>OFF</div>}/>,
  document.getElementById('root')
);
{% endhighlight %}

## 4. Jak działa JSX

Kompilator JSX, np. [Babel](https://babeljs.io/) parsuje kod JavaScript (JSX), znajduje wszystkie
odwołania do tagów html i zastępuje je wywołaniem funkcji `React.createElement` lub w przypadku, gdy
nazwa tagu jest z dużej litery, kompilator używa zmiennej komponentu.

Kod JSX

{% highlight jsx %}
ReactDOM.render(
  <div><p>Hello</p></div>,
  document.getElementById('root')
);
{% endhighlight %}

Zostanie zastąpiony przez

{% highlight javascript %}
ReactDOM.render(
  React.createElement('div', null,
                      React.createElement('p', null, 'Hello')),
    document.getElementById('root'));
{% endhighlight %}

z JSX nie korzysta tylko React, ale także inne frameworki. Takie jak np. [Preact](https://preactjs.com/), które mogą korzystać
z innej funkcji do tworzenia elementów w wynikowym kodzie JavaScript, dlatego np. [Babel](https://babeljs.io/)
posiada dyrektywę, za pomocą której można np. zmienić domyślny `React.createElement` na funkcje `h`,
z której korzysta Preact:

{% highlight javascript %}
/** @jsx h */
{% endhighlight %}

## 5. Jak się odwoływać do elementów w komponencie

Do tworzenia referencji w react służy atrybut `ref`, a jeśli dodaje się eventy do tego samego elementu, można skorzystać
z `e.target`, gdzie `e` jest to event (argument funkcji obsługi zdarzenia).

{% highlight jsx %}
class ResetInput extends React.Component {
    constructor() {
        super();
        this.reset = this.reset.bind(this);
        this.change = this.change.bind(this);
    }
    reset() {
        this.refs.text_field.value = '';
    }
    change(e) {
        this.props.onChange && this.props.onChange(e.target.value);
    }
    render() {
        return <div>
            <button onClick={this.reset}>reset</button>
            <input ref="text_field" onKeyUp={this.change}/>
        </div>
    }
}
{% endhighlight %}

`ref` jako ciąg znaków jest to stare wolniejsze API, aktualnie powinno się stosować funkcje, np.:

{% highlight jsx %}
class ResetInput extends React.Component {
    constructor() {
        super();
        this.reset = this.reset.bind(this);
        this.change = this.change.bind(this);
    }
    reset() {
        this.input.value = '';
    }
    change(e) {
        this.props.onChange && this.props.onChange(e.target.value);
    }
    render() {
        return <div>
            <button onClick={this.reset}>reset</button>
            <input ref={node => this.input = node} onKeyUp={this.change}/>
        </div>
    }
}
{% endhighlight %}

Istnieje także mechanizm `React.forwardRef`, który umożliwia przekazywanie referencji do komponentu z zewnątrz.
Referencje tworzy się za pomocą `React.createRef`. Przykład z dokumentacji Reacta:

{% highlight jsx%}
const FancyButton = React.forwardRef((props, ref) => (
  <button ref={ref} className="FancyButton">
    {props.children}
  </button>
));

// You can now get a ref directly to the DOM button:
const ref = React.createRef();
<FancyButton ref={ref}>Click me!</FancyButton>;
{% endhighlight %}

Gdy przycisk zostanie utworzony i ref podpięty, `ref.current` będzie zawierał odwołanie do elementu button.

`React.createRef()` można także stosować wewnątrz jednego komponentu, przykład:

{% highlight jsx%}
class MyInput extends React.Component {
  constructor(props) {
    super(props);

    this.inputRef = React.createRef();
  }

  render() {
    return <input type="text" ref={this.inputRef} />;
  }

  componentDidMount() {
    this.inputRef.current.focus();
  }
}
{% endhighlight %}

## 6. Omów cykl życia komponentu w React.js

Komponent w React może być w 3 stanach

1. Montowania - komponent jest dodany do drzewa DOM
wywoływane są funkcje:
* constructor()
* componentWillMount()
* render()
* componentDidMount()

2. Update-u - zmiana właściwości (ang. props) powoduje, że powinien zmienić swój stan
wywoływane są funkcje:
* componentWillReceiveProps()
* shouldComponentUpdate()
* componentWillUpdate()
* render()
* componentDidUpdate()

3. Odmontowywania - komponent jest usuwany z drzewa DOM
wywoływana jest funkcja:
* componentWillUnmount()

Dodatkowo jest jeszcze jedna funkcja dodana w React 16 - `componentDidCatch`, który wywołuje się,
gdy w funkcji render zostanie wyrzucony wyjątek, niestety nie działa razem z funkcjami obsługi zdarzeń.

{% highlight jsx %}
class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      errorInfo: null
    };
  }

  componentDidCatch(error, errorInfo) {
    this.setState({
      error: error,
      errorInfo: errorInfo
    });
  }

  render() {
    if (this.state.error) {
      // gdy wystąpi błąd wyrenderuj błąd
      return (
        <div>
          <h2>Coś się stało:</h2>
          <p>
            {this.state.error && this.state.error.toString()}
          </p>
          <div>Stos wyjątku:</div>
          <pre>{this.state.errorInfo.componentStack}</pre>
        </div>
      );
    }
    // normalne wyrenderowanie dzieci komponentu
    return this.props.children;
  }
}
class Button extends React.Component {
    constructor(props) {
        super(props);
        this.state = {count: 0};
        this.click = this.click.bind(this);
    }
    click() {
        this.setState({count: this.state.count + 1});
    }
    render() {
        // po 5 kliknieciach zostanie zwrócony błąd
        if (this.state.count >= 5) {
            throw Error('Błąd w render button');
        }
        return <button onClick={this.click}>click</button>
    }
}
ReactDOM.render(
  <ErrorBoundary>
    <Button/>
  </ErrorBoundary>,
  document.getElementById('root')
);
{% endhighlight %}

## 7. Co to jest Flux i jaka jest różnica między Flux a Redux

Flux jest to architektura aplikacji zaproponowana przez Facebook-a, twórcy frameworka React.js.
Flux nie jest to konkretna biblioteka ale architektura, której głównym elementem jest funkcja Dispatcher,
A całość używa architektury podobnej do Publish/Subscribe lub EventEmitter. Flux korzysta z jedno kierunkowego
przepływu danych w celu utrzymywania stanu aplikacji.

I ile Flux jest to nazwa architektury to Redux jest to już biblioteka, która implementuje ją tą.

## 8. Jak działa Redux

Biblioteka ta składa się z takich elementów jak Stan aplikacji (ang. Store), Reducer-y oraz Akcje.
* Reducer jest to funkcja, która zwraca nowy stan bazując na starym. Przyjmuje dwa argumenty, poprzedni stan oraz akcje,
* Akcje są to obiekty, które zostają przekazane do reducer-a, na podstawie ich typu powinien być zwrócony inny nowy stan
  aplikacji,
* Store jest to obiekt który posiada mi. takie funkcje:
  * getState - zwraca aktualny stan,
  * subscribe - służy do dodawania nowej funkcji, która zostanie wywołana, gdy zmieni się stan,
  * dispatch - do funkcji przekazujemy akcje i zostanie zmieniony stan.

Biblioteka działa niezależnie od jakiegokolwiek frameworka. Można jej np. używać z Angular-em. Aby użyć biblioteki razem
z React-em, należy dodatkowo użyć biblioteki ReactRedux oraz użyć jej dwóch funkcji `connect` oraz `Provider`.

`Provider` jest to komponent, który posiada właściwość o nazwie `store`, który udostępnia stan
aplikacji komponentom, działa tak jak ErrorBoundary (tzn. że `Provider` to komponent, który
opakowuje inne komponenty) i korzysta z Context API React-a. Natomiast funkcja `connect`, służy jako
wrapper komponentów.
Przekazuje się do niej dwie funkcje:
* mapStateToProps - jest to funkcja, która dostaje jako argument, stan aplikacji i zwraca obiekt bazujący na stanie,
* mapDispatchToProps - jest to funkcja, która dostaje `dispatch` Reduxa jako argument i zwraca obiekt z funkcjami, które wywołują `dispatch` z odpowiednimi akcjami czyli dodają funkcje zmiany stanu aplikacji.

Funkcja connect, jest to komponent wyższego poziomu czyli jest to funkcja która przyjmuje zwykły komponent i zwraca nowy komponent, który ma dostęp do stanu z Reduxa, poprzez dwie funkcje `mapStateToProps` oraz `mapDispatchToProps`.

{% highlight jsx %}
const {connect, Provider} = ReactRedux;
const mapStateToProps = state => {
    return {
        list: state.visible ? state.list : []
    }
};
const mapDispatchToProps = (dispatch, props) => {
    return {
        toggleList: visible => {
            dispatch({type: 'TOGGLE_VIEW', visible});
        }
    }
};
class List2 extends React.Component {
    constructor() {
        super();
        this.change = this.change.bind(this);
    }
    change(e) {
        this.props.toggleList(e.target.checked);
    }
    render() {
        return <div>
            <input type="checkbox" onChange={this.change}/>
            <ul>{this.props.list.map(e => <li>{e}</li>)}</ul>
        </div>
    }
}
const VisibleList = connect(
    mapStateToProps,
    mapDispatchToProps
)(List2);
const reducer = function(state = {list: [1,2,3,4], visible: false}, action) {
    if (action.type === 'TOGGLE_VIEW') {
        // ważne jest aby zawsze zwracać nowy stan
        // bez {} jako pierwszy argument aplikacja nie zadziała
        state = Object.assign({}, state, {visible: action.visible});
    }
    return state;
};
const store = Redux.createStore(reducer);
ReactDOM.render(
    <Provider store={store}>
        <VisibleList/>
    </Provider>,
    document.getElementById('root')
);
{% endhighlight %}

## 9. Jak działa Context API

Context API oraz Redux, który w nowszej wersji korzysta z Context API, można używać do
zminimalizowania wielokrotnego dziedziczenia propsów (Nazwane po angielsku prop drilling albo
threading), gdy jest potrzeba utrzymywania stanu w aplikacji w wielu komponentach. Context API
umożliwia stworzenie lokalnego stanu, który będzie dziedziczony przez inne komponenty w drzewie,
pomijając komponenty, które go nie potrzebują. Dlatego właśnie z został użyty w bibliotece Redux.
Działa tak jak zasięg funkcyjny (ang. `scope`), wszystkie funkcje wewnątrz mają dostęp do wszystkich
zasięgów powyżej.

Context API udostępnia funkcje `React.createContext`, która tworzy obiekt z dwoma komponentami:
`obiekt.Provider` oraz `obiekt.Consumer`. Przykładem niech będzie przypadek, gdy musimy dodać internacjonalizacje
do naszej aplikacji i wszystkie buttony muszą mieć przetłumaczony text. Nasze przyciski znajdują się na różnym poziomie
w drzewie DOM. Zakładając, że nie korzystamy z Reduxa, bez Context Api musielibyśmy przekazywać propsy z językiem
do każdego komponentu, aby dostał go każdy przycisk. Za pomocą Context API można to uprościć:

{% highlight jsx %}
var Lang = React.createContext('lang');

const translate = (lang, str) => {
   var map = {
      pl: {
         open: 'Otwórz',
         close: 'Zamknij'
      },
      en: {
         open: 'Open',
         close: 'Close'
      }
   };
   return map[lang][str];
}

function LocalizedButton(props) {
   return <Lang.Consumer>
       {lang => <button {...props}>{translate(lang, props.label)}</button>}
   </Lang.Consumer>
}

function Toolbar(props) {
  return (
    <div>
      <LocalizedButton label="open" />
    </div>
  );
}
class App extends React.Component {
    constructor(props) {
        super(props);
        this.state = {lang: 'en'};
        this.change = this.change.bind(this);
    }
    change(e) {
        this.setState({lang: e.target.value});
    }
    render() {
        return <div>
            <select value={this.state.lang} onChange={this.change}>
                <option value="pl">Polski</option>
                <option value="en">English</option>
            </select>
            <Lang.Provider value={this.state.lang}>
                <Toolbar/>
            </Lang.Provider>
        </div>
    }
}
ReactDOM.render(
    <App/>,
    document.getElementById('root')
);
{% endhighlight %}

## 10. Co to są kontrolowane komponenty

Są to komponenty np. elementy formularzy input, select lub textarea, których wartość (właściwość value) pochodzi od
React-a dlatego, gdy ich stan się zmieni wyrenderują się ponownie z nowych stanem (oczywiście dzięki Virtual DOM nie
zostanie utworzony nowy element tylko zmieni się jego właściwość value). Jest to realizowane w taki sposób że pod value
jest np. wartość ze state a event np. onChange lub onKeyUp zmienia ten stan.

{% highlight jsx %}
class ControlInput extends React.Component {
    constructor(props) {
        super(props);
        this.state = {value: ''};
        this.change = this.change.bind(this);
    }
    change(e) {
        this.setState({value: e.target.value});
    }
    render() {
        return <div><input onKeyUp={this.change} value={this.state.value}/></div>
    }
}
{% endhighlight %}

Kontrolowane mogą być także własne komponenty, gdzie wartość komponentu pobierana jest z zewnątrz np. z
propsów. Komponent nie koniecznie musi renderować się od nowa po zmianie stanu. Może także uaktualnić się w inny sposób,
np. gdy jest to komponent opakowujący jakąś bibliotekę. Tutaj przykład
[Komponentu kontrolowanego, opakowującego jQuery Terminal](https://codepen.io/jcubic/pen/xPepee).

## 11. Jak działa Wirtualny DOM

Wirtualny DOM jest to reprezentacja (w pamięci) prawdziwego drzewa DOM. Operacje wykonywane są na Wirtualnym DOM i gdy
coś się zmieni, wykonywane jest porównywanie drzew (ang. diff), a następnie najmniejsza liczba akcji potrzebna do tego
aby oba drzewa były takie same. Algorytm, który używa React w celu uaktualnienia natywnego DOM, nazywany jest
po angielsku reconciliation, co można przetłumaczyć jako pojednanie. Więcej o algorytmie możesz przeczytać
[w artykule z oficjalnej dokumentacji](https://reactjs.org/docs/reconciliation.html)

## 12. Jak działa obsługa zdarzeń w React

Zdarzenia w React działają podobnie do tych w natywnym DOM, z pewnymi róznicami
* nazwy zdarzeń pisane są camel case-em,
* do atrybutu zdarzenia przekazuje się funkcje a nie ciąg znaków,
* argument do funkcji obsługi zdarzenia dostaje obiekt klasy `SyntheticEvent`, który posiada odwołanie do oryginalnego
zdarzenia w polu `nativeEvent` oraz `target` wskazujący obiekt, który wywołał zdarzenie.

Ważną rzeczą jest to że zdarzenia w React nie są unikalne, tylko używane ponownie z innymi wartościami.
Ta właściwość nazywa się Event Pooling, tzn. że nie można używać zdarzeń wewnątrz funkcji asynchronicznej np. `fetch`,
ponieważ w momencie gdy asynchroniczna akcja się wywoła obiekt zdarzenia może się zmienić. Zastosowano taki mechanizm
ze względów optymalizacyjnych. Można jednak ten mechanizm wyłączyć poprzez wywołanie `event.persist()`.

## 13. Czym się różni komponent od elementu

Komponent jest to funkcja albo klasa dziedzicząca po `React.Component`, która ma jakąś logikę lub/i
zawiera inne komponenty oraz elementy.  Natomiast element jest to obiekt, który ma swój odpowiednik
w DOM np. div, span albo input. Jest to jednak uproszczenie myślowe ponieważ sam React nie używa DOM
dopiero dodatkowa biblioteka ReactDOM dodaje taką możliwość (jeśli jesteś zainteresowany dokładnym
znaczeniem elementu w react musiałbyś zajrzeć do kodu źródłowego React'a, ale jedna wydaje mi się, że
element jako element w DOM jest właściwym wyjaśnieniem chociaż nieprecyzyjnym), jednak nie ma
wątpliwości że Elementem będzie także Web Komponentem, ponieważ on także będzie miał swój odpowiednik
w DOM. Elementy występują tylko w JSX, w wynikowym JavaScript-cie zostają zastąpione przez wywołanie
funkcji `React.createElement`, gdzie pierwszy argument to nazwa taga.

## 14. Do czego służy `setState`

Metoda ta służy do zmiany wewnętrznego stanu komponentu, samo przypisanie do `this.state` nowego stanu albo zmiana
wartości jednej z jego właściwości nie sprawi, że komponent się re-renderuje. Stan komponentu najczęściej stosuje się gdy
nie potrzebujemy wysyłać go na zewnątrz komponentu.  W przeciwnym przypadku raczej trzeba by zastosować Reduxa albo
Context API.


## 15. Co to jest React Fiber

React Fiber jest no nowy ulepszony silnik w React 16, który polepsza działanie animacji, gestów oraz layoutu, jego
główną cechą jest tzw. przyrostowe renderowanie, dzięki któremu można rozbić renderowanie na kilka ramek (ang. frames),
realizowane jest to dzięki temu, że możliwe jest zatrzymania i wznowienie renderowania. Dało możliwość szybszego
wysłania zmian na ekran.

---

**Referencje:**
* [2 Minutes to Learn React 16's componentDidCatch Lifecycle Method](https://medium.com/@sgroff04/2-minutes-to-learn-react-16s-componentdidcatch-lifecycle-method-d1a69a1f753)
* [componentDidMakeSense — React Component Lifecycle Explanation](https://levelup.gitconnected.com/componentdidmakesense-react-lifecycle-explanation-393dcb19e459)
* [Oficjalna dokumentacja Context API](https://reactjs.org/docs/context.html)
* [Oficjalna dokumentacja Forwarding Refs](https://reactjs.org/docs/forwarding-refs.html)
* [Oficjalna dokumentacja SyntheticEvent](https://reactjs.org/docs/events.html)
* [Oficjalna dokumentacja Higher Order Components](https://reactjs.org/docs/higher-order-components.html)
* [Flux i Redux](https://typeofweb.com/2018/03/29/flux-i-redux/)

**Specjalne Podziękowania**

Dla Michała Miszczyszyna, autora bloga [typeofweb](https://typeofweb.com/), za sugestie odnoście odpowiedzi.

*[npm]: Node Package Manger
*[ES5]: ECMAScript 5
*[ES6]: ECMAScript 6
*[DOM]: Document Object Model
