---
layout: post
title:  "Wszystko co powineneś wiedzieć o Funkcjach w JavaScript"
date:   2014-08-06 10:35:19
categories: 
tags: javascript front-end funkcje
author: jcubic
---

W ninejszym artykule przedstawię wszyskie aspekty używania funkcji w języku
JavaScript. Omówie czym się różni funkcja od procedury, co to są typy
pierwszo-klasowe, funkcje wyższego rzędu, domknięcia leksyklane, funkcje jako
metody oraz kontekst funkcji czyli zmienna specjalna **this**.

<!-- more -->

## Funkcje a procedury
        
Funkcja czyli odpowiednik matematycznej funkcji jest to objekt, który
dla tego samego argumentu zawsze zraca tą samą wartość. Z puntku widzenia
programistycznego tego typu funkcje są nazywane czystymi (ang. pure),
nie wykonują one żadnych dodatkowych czyności, tzn. nie mają efektów ubocznych
(ang. side effects).

Procedura natomiast jest to pewien wykonywany proces, np. dwie funkcje,
które używają innego sposobu na obliczenie jakiejś wartości będą z matematycznego
punktu widzenia tą samą funkcją, natomiast będą to dwie różne procedury
na obliczenie danej wartości.

W programowaniu przyjęło się, że procedura to funkcja, która nie zwraca
wartości, głównie z powodu języka Pascal, którego uczą w szkołach.

## Funkcje są typem pierwszo klasowym

W języku JavaScript funkcje są typem pierwszo klasowym oznacza, funkcja jest
takim samym obiektem jak np. liczba, czy ciąg znaków. Można je umieszczać
w tablicach, przypisywać do zmiennych, przekazywać jako argumenty do funkcji.
Można także tworzyć funkcje, które zwracają funkcje. Są to tzw. funkcje wyższego
rzędu.

## Funkcje wyższego rzędu

Najbardziej znanymi przykładami funkcji wyższego rzędu są funkcje takie jak:
**map**, **filter**, **reduce** czy **forEach**, które zostały dodane do wersji
ES5 języka JavaScript. Wersja ES5 powinna być dostąpna w każdej nowej przglądarce
internetowej. Funkcje te są dostepne jako metody tablic poniewarz operują one
właśnie na tablicach.

Dzięki funkcji **map** możemy zamienić tablicę na nową tablicę przetworząną
w pewien sposób np:

{% highlight javascript %}
var integers = [1,2,3,4,5,6,7,8];
var integers_plus_10 = integers.map(function(integer) {
    return integer+10;
});
console.log(integers_plus_10);
// => [11, 12, 13, 14, 15, 16, 17, 18]
{% endhighlight %}

W powyższym kodzie nowa tablica będzie zwaierała listę liczb w której każda
została zwiększona o 10. Funkcja **filter** zwraca nowa tablicę w której znajdą
się tylko takie elementy dla, których funkcja przekazana jako argument zwróci
wartość **true**. Funkcja **reduce** łączy wszystkie elementy ze sobą. W wyniku
jej działania otrzymujemy jedną wartość, dzięki niej możemy obliczyć np. sumę
liczb:

{% highlight javascript %}
integers.reduce(function(sum, integer) {
    return sum+integer;
});
// ==> 36
{% endhighlight %}

Funkcja **forEach** działa jak map, przetwarzając każdy elemente talicy nie
zwraca ona jednak nowej tablicy.

Inną ciekawą funkcją, którą można użyć jako przykład funkcji wyższego rzędu jest
funkcja **curry**, często jako przykład kodu implementującego tą funkcję podaje
się błędnie __partial application__. Funkcja **curry** przyjmuje funkcje jako
argument a wynikiem jest seria funkcji w której każda przyjmuje jeden argument
dopóki wszystkie argumenty nie zostaja wyczerpane wtedy wywoływana jest nasza
oryginalna funkcja i zwracany jest wynik. Kod funkcji **curry** przedstawiono poniżej:

{% highlight javascript %}
function curry(fn) {
    var args = [];
    return function curring() {
        args = args.concat([].slice.call(arguments));
        if (args.length >= fn.length) {
            return fn.apply(this, args);
        } else {
            return curring;
        }
    };
}
{% endhighlight %}

Można ją użyć np w ten sposób:

{% highlight javascript %}
function add(a, b, c, d) {
   return a+b+c+d;
}

curry(add)(1)(2)(3)(4);
// ==> 10
{% endhighlight %}

Ciekawszym rozwiązaniem jest funkcja, która działa podobnie ale na każdym etapie
można wywoływać funkcję z wieloma argumentami lub bez argumentów, tego typu
funkcja dostępna jest w bibliotece [wu.js](https://fitzgen.github.io/wu.js/)
i nazywa się **autoCurry**. Zachęcam do zajrzenia do kodu źródłowego biblioteki.

## Funkcje bez nazwy

Jak widzieliście w poprzednich przykładach z funkcją **map** czy **reduce**, można
zadeklarować funkcję bez nazwy, jest to tzw. funkcja anonimowa. W języku JavaScript
w zależności od miejsca w którym się znajdzie deklaracja, funkcja może być traktowana
jako wyrażenie lub instruckja. Jeśli wstawimy ją samą mimo że nie będzie miała nazwy
będzie to instruckja dlatego nie trzeba na końcu wstawiać średnika.

{% highlight javascript %}
function() {

}
{% endhighlight %}

Jeśli natomiast przypiszemy ją do zmiennej będzie to już wyrażenie:

{% highlight javascript %}
var foo = function() {

};
{% endhighlight %}

## Domknięcia leksykalne

Domknięcia leksykalne (ang. closures) są to funkcje, wewnątrz których mamy dostęp
do zmiennych, które zostały zadeklarowne na zewnątrz funkcji mimo że zakres ich
istnienia się już zakończył. Istnieją one w środowisku, które jest __"doczepione"__
do funkcji. Często spotykanym przykładem jest licznik:

{% highlight javascript %}
function counter(init) {
    var start = init;
    return function(inc) {
        inc = inc || 1;
        start+=inc;
        return start;
    };
}
var start10 = counter(10);
var start2 = counter(2);
console.log(start10());
// ==> 11
console.log(start10(5));
// ==> 16
console.log(start2(2));
// ==> 4
console.log(start2(2));
// ==> 6
{% endhighlight %}

W powyższej funkcji zmienna **start** zakończyła swój żywot, ale jej referencja
jest zamknięta wewnątrz środowiska, do którego ma dostęp funkcja anonimowa, która
została zwrócona przez **counter**. Powyższą funkcję można trochę uprościć:

{% highlight javascript %}
function counter(init) {
    return function(inc) {
        return init+=inc||1;
    };
}
{% endhighlight %}


## IIFE - Immediately-Invoked Function Expression

Natychmiastowo-wywoływwane wyrażenie funkcyjne, jest często stosowane w jezyku
JavaScript, ponieważ w języku tym bloki nie tworzą nowego zakresu zmiennych, tak
jak to ma miejsce w przypadku języka **Java** czy **C**. Stosuje się je także
aby odizolować część kodu od reszty programu. Często stosują ją twórcy bibliotek
np. cały kod biblioteki **jQuery** znajduje się wewnąrz takie funckcji, tworząc
pluginy jQuery cześto stosujemy poniższy kod:

{% highlight javascript %}
(function($) {
    $.fn.plugin_name = function() {
        // kod pluginu
    };
})(jQuery);
{% endhighlight %}

Dzięki temu możemy wewnątrz tworzyć prywatne zmienne dostępne tylko z wnętrza
naszego pluginu. Dodatkowo jeśli jeśli użytkownik pluginy używa wywołania
**jQuery.noConflict()** w naszym kodzie nadal będziemy mieli dostęp do zmiennej
dolar poniewarz jest to zmienna lokalna (dostępna jako parametr IIFE).

W powyższy przykładnie funkcja jest wyrażeniem poniewarz jest objęta za pomocą
nawiastów, istnieje kilka sposobów wymyszenia aby funkcja była wyrażeniem,
często spotyka się także użycie wykrzyknika na początku:

{% highlight javascript %}
!function() {

}();
{% endhighlight %}

W jezyku JavaScript często stosuje się tego typu funkcje wewnątrz petli, aby
utworzyć **środowisko leksykalne**. Jeśli mamy poniższy kod:

{% highlight javascript %}
for (var i = 0; i <= 9; i++) {
    setTimeout(function() {
        console.log(i);
    }, i*1000);
}
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
// ==> 10
{% endhighlight %}

To na konsoli zobaczymy co jedną sekundę 10 wartości 10 a nie jak byśmy się
spodziewali wartości od 0 do 10. Jest to spowodowane tym że petla for nie tworzy
nowego środowiska i dla każdego wywołania **console.log** mamy tą samą referencje.
Po jednej sekundzie nasza pętla się już zakończy i w zmiennej **i** będzie się
znajdowała wartość 10, którą następnie wyświetlą wszystkie opóźnione funkcje.

Aby temu zapobiec stosuje się anonimową funkcje, która jest natychmiast wywoływana
wewnątrz, której zostanie stworzone nowe środowisko z naszą zmienną. Dzięki czemu
każde wywołanie **console.log** będzie miało swoją własną zmienną.

{% highlight javascript %}
for (var i = 0; i <= 9; i++) {
    (function(i) {
        setTimeout(function() {
            console.log(i);
        }, i*1000);
    })(i);
}
// ==> 0
// ==> 1
// ==> 2
// ==> 3
// ==> 4
// ==> 5
// ==> 6
// ==> 7
// ==> 8
// ==> 9
{% endhighlight %}

Wewnątrz samowywołującego się wyrażenia funkcyjnego zmienna nie musi nosić nazwy
**i** może to być np. **x** ale dzięki temu pokazujemy, że zmienna w pętli i zmienna
w **console.log** ma tą samą wartość.

## Funkcja jest obiektem, który może mieć właściwości

Funkcja jest takim samym obiektem jak np. ciąg znaków. Posiada wbudowane metody
i właściwości. Możemy także dodawać nowe funkcje i właściwości do pojedynczej
funkcji jak i do każdej funkcji dzięki dziedziczeniu prototypowemu, które
zostanie omówione w innym artykule.

Każda funkcja posiada między innymi wbudowaną właściwość **length**, która
określa liczbę parametrów, z którą została zadeklarowana czy **name**, która
określa nazwę funkcji. Funkcje posiadają takie metody jak **bind**, **apply**,
czy **call**, które zostaną omówione w kolejnej sekcji.

Wewnątrz funkcji mamy także dostęp do specjalnej zmiennej **arguments**, która
jest podobna do tablicy ale tablicą nie jest. Dzięki niej mamy dostęp do wszystkich
argumentów wywołanej funkcji, dzięki czemu możemy tworzyć funkcje ze zmienną liczbą
argumentów.

## Kontekst funkcji oraz funkcja jako konstruktor

Funkcja może być także klasą znaną z innych obiektowych języków programowania
a dokładnie może być konstruktorem klasy. Same klasy nie istnieją w języku
JavaScript, będą natomiast wprowadzone w wersji
[ECMAScript](https://pl.wikipedia.org/wiki/ECMAScript) 6, dostępne są także
w jezyku [CoffeeScript](https://pl.wikipedia.org/wiki/CoffeeScript), który
[kompiluje](https://pl.wikipedia.org/wiki/Kompilator) się do kodu JavaScript.

Aby utworzyć konstruktor piszemy zwykłą funkcje, mamy wewnątrz niej jednak
dostęp do naszego obiektu pod zmienną specjalną **this**.

{% highlight javascript %}
function Person(name) {
    this.name = name;
}
var jan = new Person('Jan Kowalski');
{% endhighlight %}

Tak jak w innych językach do utworzenia nowego obiektu stosuje się słowo kluczowe
**new**. Jeśli go nie zastosujemy, zmienną **this**, czyli tzw. kontekstekstem
będzie obiekt window, czyli obiekt globalny.

Tak jak w innych językach obiektowych możemy tworzyć metody, czyli funkcje, które
wywołujemy w kontekscie jakiegoś obiektu. Możemy bezpośrednio dodać funkcje do
zmiennej this lub dodać do tzw. prototypu funkcji. Prototypy wykraczają poza
zakres niniejszego artykułu, omówimy je przy innej okazji. W poniższym kodzie
zdefiniowano metodę **getName**, która odwołuje się zo pola **name** za pomocą
zmiennej **this**:

{% highlight javascript %}
function Person(name) {
    this.name = name;
    this.getName = function() {
        return this.name;
    };
}
var jan = new Person('Jan Kowalski');
console.log(jan.getName());
// => Jan Kowalski
{% endhighlight %}

Możemy wywołać naszą metodę korzystając z notacji kropki. Jak jednak
dowiedzieliście się wcześniej funkcje są typem pierwszo klasowym. Co stanie więc
gdy przypiszemy metodę do innej zmiennej i spróbujemy ją wywołać. Moglibyśmy czegoś
takiego potrzebować np. gdybyśmy chcieli przekazać metodę do funkcji wyższego rzędu.

{% highlight javascript %}
function Person(name) {
    this.person_name = name;
    this.getName = function() {
        return this.person_name;
    };
}
var jan = new Person('Jan Kowalski');
var jan_name = jan.getName;
console.log(jan_name());
// ==> undefined
{% endhighlight %}

Wyświetli się wartość **undefined**, poniewarz zmienną **this** będzie znowy
obiekt globalny **window**, który nie ma zdefiniowanej zmiennej **person_name**.

W jezyku JavaScript możemy zmieniać kontekst, czyli zmienną **this** wewnątrz
funkcji. Służą do tego trzy funkcje **call**, **apply** oraz **bind**. Funkcje
**call** oraz **apply** są bardzo podobne wywołują one daną funkcje, zmieniając
kontekst. Do funkcji **call** przekazujemy listę argumentów po przecinku natomiast
do funkcji **apply** tablicę argumentów. Natomiast funkcja **bind** zwraca nową
funkcję w której kontekst jest zmieniony.

W naszym poprzednim przykładzie aby wywołać naszą funkcje **jan_name** w kontekscie
obiekty jan można skorzystać z jednego z poniższych wywołań:

{% highlight javascript %}
console.log(jan_name.apply(jan));
console.log(jan_name.call(jan));
{% endhighlight %}

W obu przypadkach nie przekazano argumentów. Można także skorzystać z funkcji
**bind** aby od razu utworzyć funkcję z właściwym kontekstem:

{% highlight javascript %}
var jan_name = jan.getName.bind(jan);
{% endhighlight %}

Dzięki funkcji **bind** możemy przekazywać metody jako funkcje do innych funkcji.
