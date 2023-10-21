---
layout: post
title:  "Jak zrobić stronę, która wygląda jak terminal"
date:   2023-10-21 23:08:02+0200
categories:
tags: javascript jquery terminal cli
author: jcubic
description: Jak w prosty sposób stworzyć stronę internetową która wygląda jak terminal (np. ten z systemy GNU/Linux)
image:
 url: "/img/terminal.png"
 alt: "Grafika przestawiająca widok wiersza poleceń, z komendą cat i wyświetlonym zdjęciem kota, oraz komendą hampster która zwraca bład o nieznanym poleceniu"
 width: 800
 height: 453
---

W dzisiejszych czasach komputery zdominowane są przez GUI, czyli interfejs graficzny.
Istnieje jednak inny rodzaj interfejsu, który ma bardzo długą historię.
Znają go przede wszyscy administratorzy systemów albo zaawansowani użytkownicy.
Mowa o terminalu, dzięki któremu można osiągnąć zdumiewającą produktywność oraz
wykonywać działania, które nie zostały bezpośrednio zaprogramowane przez
programistów dzięki tzw. językowi powłoki.

<!-- more -->

## Historia terminala

Historia terminala sięga korzeniami daleko, aż do początków komputerów typu
mainframe oraz systemów operacyjnych z podziałem czasu. Początkowo była to
drukarka połączona z klawiaturą. Później drukarkę zastąpiono monitorem CRT.
Gdy przestano używać fizycznych terminali zaczęto używać terminali programowych,
czyli tzw. emulatorów terminali, aby umożliwić te same możliwości.

Siła interfejsu CLI czyli wiersza poleceń dostępnego w emulatorach terminali,
najlepiej widać na przykładzie Systemów uniksowych (np. GNU/Linux oraz MacOSX).
Linuksowy wiersz poleceń jest aż tak użyteczny, że twórcy systemu operacyjnego
Windows dodali system (WSL) który pozwala na pełną emulacje prawdziwego systemu
GNU/Linux, w podstawowej formie jest to wiersz poleceń.

## Jak dodać stronę która wygląda jak wiersz poleceń

Aby stworzyć stronę internetową, która wygląda jak terminal, np. GNU/Linux czy PowerShell,
najprościej posłużyć się gotową biblioteką, jak np.
[jQuery Terminal Emulator](https://terminal.jcubic.pl/).

Aby użyć biblioteki trzeba utworzyć stronę HTML. Która może wyglądać tak:

{% highlight html %}
<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.terminal/js/jquery.terminal.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery.terminal/css/jquery.terminal.min.css"/>
</head>
<body>
<script>
/* Tutaj wstawisz swój kod JavaScript */
</script>
</body>
</html>
{% endhighlight %}

Strona dodaje bibliotekę jQuery, używaną przez jQuery Terminal. Mimo że ludzie odradzają w 2020 używanie
jQuery, dzięki temu że jQuery Terminal używa tej biblioteki sam kod jest o wiele krótszy i prostszy.

### Pierwsze polecenie

Aby utworzyć pierwsze polecenie wystarczy dodać taki kod:

{% highlight javascript %}
$('body').terminal({
    hello: function(what) {
        this.echo('Witaj, ' + what +
                  '. To jest Terminal.');
    }
}, {
    greetings: 'Mój pierwszy Terminal'
});
{% endhighlight %}

Wynik polecenia hello wygląda tak:

![Przykład użycia biblioteki jQuery Terminal Emulator](/img/terminal_01.png)

Tudaj zobaczysz [demo live](https://codepen.io/jcubic/pen/QWmbZOY?editors=0010).

### Polecenie które wyświetla obrazek

Innym przykładem może być polecenie `cat` (ang. kot), które wyświetla zdjęcie:

{% highlight javascript %}
$('body').terminal({
    cat: function() {
        this.echo($('<img src="https://placekitten.com/408/287">'));
    }
}, {
    greetings: 'Mój pierwszy Terminal'
});
{% endhighlight %}

Zwróć uwagę na `$()` wewnątrz wywołania `echo`, jest ono potrzebne. Domyślnie Terminal nie wyświetla HTMLa,
ze względów bezpieczeństwa. Przekazując obiekt jQuery można jednak wyświetlić go na ekranie.
Istnieje także drugi sposób:

{% highlight javascript %}
term.echo('<img src="https://placekitten.com/408/287">', { raw: true });
{% endhighlight %}

Nie jest to domyślne zachowanie ponieważ, dużo się dzieje w funkcji echo, jak np. zawijanie tekstu
czy formatowanie, które nie działa w przypadku wyświetlania HTML, druga sprawa to bezpieczeństwo.

Więcej o bezpieczeństwie aplikacji przeczytasz w moim artykule:

[O atakach Hakerskich i jak się przed nimi zabezpieczyć](https://jcubic.pl/2018/01/bledy-aplikacji-internetowych.html).

Wróćmy jednak do naszego terminala. Wyświetlanie pojedynczego polecenia które zwraca za każdym razem ten sam
obrazek nie jest bardzo ekscytujące. Poniższy przykład pokazuje jak dodać argumenty do polecenia:

{% highlight javascript %}
$('body').terminal({
    cat: function(width, height) {
        const img = $('<img src="https://placekitten.com/' +
                      width + '/' + height + '">');
        this.echo(img);
    }
}, {
    greetings: 'Mój pierwszy Terminal'
});
{% endhighlight %}

Poniżej zdjęcie jak wygląda nasz terminal po wykonaniu polecenia "cat 300 300"

![Wiersz poleceń oraz obrazek kota](/img/terminal_02.png)

[Demo Live](https://codepen.io/jcubic/pen/dymogJR?editors=0010).

### Polecenia asynchroniczne

Niektóre polecenia są asynchroniczne, tzn. wykonują się co może chwilę trwać a potem wyświetlają wynik.
Warto zatrzymać na ten czas terminal aby nic innego nie robił i czekał na odpowiedź. Tak np.
dzieje się w przypadku obrazków które nie ładują się od razu, chociaż w przypadku szybkiego internetu,
tak może to wyglądać.

Aby zatrzymać działanie terminal można użyć dwóch rodzajów API:

#### 1. funkcje pause/resume

{% highlight javascript %}
$('body').terminal({
    cat: function(width, height) {
        const img = $('<img src="https://placekitten.com/' +
                      width + '/' + height + '">');
        img.on('load', this.resume);
        this.pause();
        this.echo(img);
    }
}, {
    greetings: 'Mój pierwszy Terminal'
});
{% endhighlight %}

#### 2. Obietnice

{% highlight javascript %}
function get_image(url) {
    return new Promise(function(resolve, reject) {
        const img = $('<img src="' + url + '"'/>');
        img.on('load', () => resolve(img));
        img.on('error', reject);
    });
}

$('body').terminal({
    cat: function(width, height) {
        return get_image('https://placekitten.com/' + width +
                         '/' + height);
    },
    dog: function(width, height) {
        return get_image('https://placedog.net/' + width +
                         '/' + height);
    }
}, {
    greetings: 'Mój pierwszy Terminal'
});
{% endhighlight %}

Efekty użycia obu API będą takie same tzn. po wpisaniu polecenia (co może być widoczne
przy słabszym internecie lub przy większych zdjęciach) terminal się zatrzyma,
kursor się schowa i po załadowaniu się obrazka pojawi się kursor z powrotem.

[Demo Live](https://codepen.io/jcubic/pen/KKopGZL?editors=0010).

### Polecenie wykonujące zapytanie HTTP w przeglądarce

Aby wykonać zapytanie
[HTTP](https://pl.wikipedia.org/wiki/Hypertext_Transfer_Protocol) w przeglądarce (czyli tzw.
[AJAX](https://pl.wikipedia.org/wiki/AJAX)), można użyć funkcji `fetch`.
Poniżej przykład polecenia pobierający tytuł strony internetowej. Aby coś takiego zadziałało
dla domeny innej niż ta, na której znajduje się strona, potrzebne jest mechanizm
[CORS](https://pl.wikipedia.org/wiki/Cross-Origin_Resource_Sharing).

{% highlight javascript %}
$('body').terminal({
    title: function() {
        return fetch('https://terminal.jcubic.pl')
            .then(r => r.text())
            .then(html => html.match(/<title>([^>]+)<\/title>/)[1]);
    }
}, {
    greetings: 'Mój pierwszy Terminal\n'
});
{% endhighlight %}

Poniżej wynik wykonania polecenia:

![Przykład biblioteki jQuery Terminal Emulator](/img/terminal_03.png)

Do poleceń można także dodawać opcje tak jak w poleceniach systemu unix.

{% highlight javascript %}
$('body').terminal({
    title: function(...args) {
        const options = $.terminal.parse_options(args);
        return fetch(options.url || 'https://terminal.jcubic.pl')
            .then(r => r.text())
            .then(html => html.match(/<title>([^>]+)<\/title>/)[1]);
    }
}, {
    checkArity: false,
    greetings: 'Mój pierwszy Terminal\n'
});
{% endhighlight %}

W powyższych przykładzie dodano opcje biblioteki `checkArity` dzięki temu,
biblioteka nie będzie wyświetlała błędu gdy przekażemy inną liczbę argumentów.
Domyślnie funkcja title będzie o sobie informowała że nie ma żadnych parametrów
(właściwość `length` będzie równe 0). Polecenie title z powyższego kodu można
wywoływać z opcjami lub bez.

Poniżej wynik działania dwóch rodzajów poleceń:

![Przykład polecenia AJAX dla biblioteki jQuery Terminal](/img/terminal_04.png)

[Demo Live](https://codepen.io/jcubic/pen/wvmaQvZ?editors=0010)

Ostania opcja która warta jest opisania jest auto-uzupełnienie poleceń,
po naciśnięciu klawisza tabulacji. Aby skorzystać z tej funkcji wystarczy dodać opcję
`completion`.

{% highlight javascript %}
$('body').terminal({
    title: function(...args) {
        const options = $.terminal.parse_options(args);
        return fetch(options.url || 'https://terminal.jcubic.pl')
            .then(r => r.text())
            .then(html => html.match(/<title>([^>]+)<\/title>/)[1]);
    }
}, {
    checkArity: false,
    completion: true,
    greetings: 'Mój pierwszy Terminal\n'
});
{% endhighlight %}

Dzięki temu po wpisaniu klawisza "t" oraz naciśnięcia klawisza tabulacji pojawi się całe polecenie.
Jeśli terminal ma więcej niż jedno polecenie które zaczyna się od takiego samego ciągu znaków.
Naciśnięcie pojedynczego klawisza tabulacji uzupełni wspólny człon, a po następnych dwóch tabulacjach
pojawi się lista dostępnych opcji tak jak to wygląda dla systemu GNU/Linux.

I to by była na tyle jeśli chodzi o podstawowe funkcje biblioteki jQuery Terminal, jest ich znacznie więcej,
warto tylko wymienić kilka (linki do dokumentacji w języku angielskim):

* [Formatowanie i kolorowanie tekstu](https://github.com/jcubic/jquery.terminal/wiki/Formatting-and-Syntax-Highlighting),
* Obsługa protokołu JSON-RPC (informacja na stronie o
  [rodzajach interpretera](https://github.com/jcubic/jquery.terminal/wiki/Getting-Started#creating-the-interpreter)),
* [Zmiana znaku zachęty](https://github.com/jcubic/jquery.terminal/wiki/Getting-Started#prompt),
* [Maskowanie haseł](https://github.com/jcubic/jquery.terminal/wiki/Getting-Started#masking-password),
* [Uwierzytelnianie](https://github.com/jcubic/jquery.terminal/wiki/Authentication),
* [Wykonywanie poleceń z JavaScript](https://github.com/jcubic/jquery.terminal/wiki/Getting-Started#executing-commands-from-javascript),
* [Kontrolowanie terminal z poziomu serwera](https://github.com/jcubic/jquery.terminal/wiki/Invoking-Commands-and-terminal-methods-from-Server),
* Zapisywanie stanu terminal w URLu i wykonywanie tak zapisanych poleceń,

Aby poznać więcej możliwości biblioteki możesz spojrzeć na [przykłady na stronie domowej](https://terminal.jcubic.pl/examples.php).
Które zawiera między innymi animacje czy
[stronę 404](https://terminal.jcubic.pl/examples.php#404) z przykładowymi poleceniami,
jak Wikipedia, które wyświetla artykuły z Wikipedii.

Na stronie Wiki możesz zapoznać się w z dwoma artykułami [Getting Started Guide](https://github.com/jcubic/jquery.terminal/wiki/Getting-Started) oraz [Advanced Tutorial](https://github.com/jcubic/jquery.terminal/wiki/Advanced-jQuery-Terminal-Tutorial).

To see more information, that was not covered in this article, check the Getting Started Guide and more advanced stuff in Advanced Tutorial.

I na koniec, aby pokazać demo innego ciekawego wyglądu Terminala które znajduje się na stronie
[Codepen](https://codepen.io/jcubic/pen/BwBYOZ).

![Wygląd Starego Terminala przy użyciu biblioteki jQuery Terminal](/img/terminal_05.png)

*[WSL]: Windows Subsystem for Linux
*[HTTP]: Hypertext Transfer Protocol
*[AJAX]: Asynchronous JavaScript and XML
*[URL]: Uniform Resource Locator
