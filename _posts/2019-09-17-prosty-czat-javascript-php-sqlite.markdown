---
layout: post
title:  "Prosty Czat w JavaScript, PHP i SQLite"
date:   2019-09-17 22:22:30+0200
categories:
tags: php sqlite javascript ajax http
author: jcubic
description: Jak napisać prosty czat w JavaScript za pomocą Server-sent events, PHP, AJAX i SQLite.
image:
 url: "/img/phone-chat-app.png"
 alt: "Grafika Wektorowa przedstawiająca symbloliczną aplikacje czatu na telefonie"
 width: 800
 height: 599
---

Server-sent events (SSE) to alternatywa dla Web Sockets (gniazd) dla serwerów, które
nie mają możliwości odpalania nic na portach. Czyli np. w przypadku zwykłych
kont współdzielonych (ang. shared hosting), które najczęściej udostępniają tylko PHP.
W tym wpisie przedstawię jak napisać prosty, nowoczesny czat w JavaScript i PHP,
korzystając z Server-sent events oraz AJAX, przy wykorzystaniu popularnej bazy danych SQLite.

<!-- more -->

## Wprowadzenie

Nasza aplikacja będzie działała w ten sposób. Będziemy mieli dwa kanały.
Ajax będzie nam służył do wysyłania informacji do serwera, z kolei Server Side Events,
użyjemy do wysyłania zdarzeń z serwera do przeglądarki. W rezultacie otrzymamy
to samo gdybyśmy korzystali z Web Socketów czyli komunikacji w dwie strony.
I to wszystko bez uciążliwości Web Socketów ponieważ nie musimy uruchamiać
demona na serwerze, który by nasłuchiwał na porcie.

Komunikacje zobrazuje poniższa ilustracja:

![Server Side Events and AJAX communication](/img/ajax-server-side-events-app.svg)

Mamy pojedyncze zapytania AJAX-em (nie interesuje nas co zwraca serwer) oraz
Pojedyncze zapytanie do serwera o strumień danych i potem już tylko dostajemy
dane z serwera, bez potrzeby ponownego zapytania.

W JavaScript API wygląda dokładnie tak jak na ilustracji. Tworzymy pojedynczą
instancje strumienia SSE, a całością zajmuje się przeglądarka. Jeśli połączenie
zostanie przerwane (zazwyczaj czas działania skryptu PHP jest ograniczony),
zostanie wysłane nowe zapytanie HTTP do serwera, ale o to już nie musimy się martwić.
Dla nas najważniejsza jest ta abstrakcja, że mamy jeden strumień i tak
powinniśmy o nim myśleć.

## Aplikacja - Front-end

Zacznijmy od front-endu aplikacji. Najpierw podstawowy szablon strony HTML:

{% highlight html %}
<!DOCTYPE html>
<html>
  <head>
    <title>Prosty Czat w JavaScript</title>
    <link rel="shortcut icon" href="favicon.ico">
    <meta name="description" content="Prosty czat w JavaScript i PHP, za pomocą Server Side Events"/>
    <link href="style.css" rel="stylesheet"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
<body>
</body>
</html>
{% endhighlight %}


Strona ma dwa meta tagi: `description`, to meta tag, który razem z `title` pojawi
się w wynikach wyszukiwania, jeśli zindeksuje ją Google. Drugi meta tag to standard,
aby strona wyświetlała się poprawnie na telefonie.

Potrzebne nam będą tylko trzy tagi, formularz, pole tekstowe `textarea` oraz pole `input`.

{% highlight html %}
<form>
<textarea readonly></textarea>
<input placeholder="what you want to say?"/>
</form>
{% endhighlight %}

Potrzebujemy formularza, aby łatwiej obsłużyć wysyłanie wiadomości na telefonie.

Teraz musimy trochę ostylować ten formularz, ale tylko trochę, bo jest to minimalistyczny
przykład czatu.

{% highlight css %}
body {
    margin: 0;
}
textarea {
    width: 100vw;
    box-sizing: border-box;
    height: calc(100vh - 40px);
    border: none;
    border-bottom: 1px solid black;
}
input {
    position: absolute;
    padding: 5px 10px;
    font-size: 18px;
    bottom: 0;
    left: 0;
    height: 40px;
    box-sizing: border-box;
    border: none;
    width: 100vw;
}
{% endhighlight %}

Ten kod sprawi, że `textarea` będzie na całą stronę, a pod nią będzie input.

Dalej podstawowy kod, który pobierze od użytkownika jego imię/nick, oraz za pomocą AJAX-a
wyśle wiadomość to serwera. Użyłem kodu Vanilla JavaScript (czyli JS bez żadnych
framework-ów i bibliotek), aby nie komplikować przykładów. Ale nic nie szkodzi,
abyś sam wprowadził ten kod np. do React-a lub Angular-a.

{% highlight javascript %}
function send(username, message) {
   const data = new URLSearchParams();
   data.append('username', username);
   data.append('message', message);
   return fetch('new.php', {method: 'POST', body: data}).then(r => r.text());
}
const input = document.getElementsByTagName('input')[0];
const form = document.getElementsByTagName('form')[0];
let username;
while (true) {
    username = prompt("What's your name?");
    if (typeof username === 'string') {
        username = username.trim();
        if (username) {
            break;
        }
    }
}
form.addEventListener('submit', function(e) {
    e.preventDefault();
    send(username, input.value);
    input.value = '';
});
{% endhighlight %}

Używamy zdarzenia `submit` oraz formularza, ponieważ tak jest łatwiej pobrać
dane od użytkownika na telefonach Android. Na tych telefonach przeglądarka
może nie wysyłać zdarzenia `keydown` oraz `keypress`, przynajmniej klawiatura,
której ja używam czyli Swift Keyboard.

Nie dodajemy tej wiadomości do pola tekstowego, załatwi nam to ten sam kod,
który odbiera dane strumieniowe z serwera.

### Server Side Events w JavaScript

Teraz najważniejsza rzecz, czyli pobieranie strumienia zdarzeń z serwera za pomocą
Server Side Events. Jeśli chciałbyś stworzyć czat np. we framework-u Angular lub React.
To poniżej jest kod, który musisz użyć. Obiekt EventSource, to główna magia SSE w przeglądarce.

{% highlight javascript %}
const textarea = document.getElementsByTagName('textarea')[0];
const eventSource = new EventSource('stream.php');
eventSource.addEventListener('chat', (e) => {
    var data = JSON.parse(e.data);
    textarea.value += data.username + '> ' + data.message + '\n';
    // scroll to bottom
    textarea.scrollTop = textarea.scrollHeight;
});
{% endhighlight %}

I to tyle cały front-end, naszej bardzo prostej aplikacji do czatu w JavaScript i PHP.

## Back-End

Kod php, ponieważ jest go trochę więcej, będzie bardziej ustrukturyzowany.

### Baza danych SQLite i nasz model danych

SQLite to bardzo prosta baza danych, która wszystko zapisuje w jednym pliku,
jest bardzo popularna, jako sposób zapisu konfiguracji. Korzysta z niej np.
Chrome/Chromium do zapisu m.i. ciasteczek. Popularna jest także w aplikacjach
wbudowanych, które mają ograniczone zasoby. Jest bardzo powszechna, jeśli używasz
współdzielonego hostingu (ang. shared hosting), sprawdź czy jest dostępna,
bardzo fajnie się z nią pracuje.


Poniżej klasa, która zwiera obsługę bazy danych SQLite. Czyli nasz model danych.

> Komentarze w kodzie, oczywiście po angielsku, jeśli zaczynasz przygodę
> z programowaniem zalecam pisanie po angielsku. Nie ma sensu pisać ich po polsku.
> Cały kod, czyli słowa kluczowe są w tym języku. Ma to znaczenie zwłaszcza w
> zespołach międzynarodowych (jeśli będziesz pracował jako programista).
> Kod Open Source, także powinien mieć komentarze po angielsku. No i można się
> też podszkolić pisząc w tym języku.

{% highlight php startinline=true %}
// -----------------------------------------------------------------------------
// :: class that represent model of messages using SQLite database
// -----------------------------------------------------------------------------
class Messages {
    function __construct($time = null) {
        // if $time is null it will return all messages we will use this when
        // showing message when app starts
        if (is_null($time)) {
            $this->time = time();
        } else {
            $this->time = $time;
        }
        $this->db = new PDO('sqlite:messages.sqlite');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!$this->table_exists('chat')) {
            $this->query("CREATE TABLE chat(username VARCHAR(300), message " .
                         "TEXT, timestamp INTEGER)");
        }
    }
    // -------------------------------------------------------------------------
    // :: function check if table exists in SQLite databse file
    // -------------------------------------------------------------------------
    private function table_exists($table) {
        $data = $this->query("SELECT name FROM sqlite_master WHERE type=" .
                             "'table' AND name = ?", array($table));
        return count($data) > 0;
    }
    // -------------------------------------------------------------------------
    // :: universal query database function that return data or
    // :: numer of rows affected
    // -------------------------------------------------------------------------
    function query($query, $data = null) {
        if ($data == null) {
            $res = $this->db->query($query);
        } else {
            $res = $this->db->prepare($query);
            if ($res) {
                if (!$res->execute($data)) {
                    throw Exception("execute query failed");
                }
            } else {
                throw Exception("wrong query");
            }
        }
        if ($res) {
            $re = "/^\s*INSERT|UPDATE|DELETE|ALTER|CREATE|DROP/i";
            if (preg_match($re, $query)) {
                return $res->rowCount();
            } else {
                return $res->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            throw new Exception("Coudn't open file");
        }
    }
    // -------------------------------------------------------------------------
    // :: function used to fetch data, you're passing what should be returned
    // -------------------------------------------------------------------------
    function fetch($values) {
        return $this->query("SELECT $values FROM chat WHERE timestamp > " .
                            "{$this->time} ORDER BY timestamp");
    }
    // -------------------------------------------------------------------------
    // :: function check if there any new data in database from after timestamp
    // -------------------------------------------------------------------------
    function hasData() {
        $data = $this->fetch("count(*)");
        return $data[0]['count(*)'] > 0;
    }
    // -------------------------------------------------------------------------
    // :: function return data from last time and reset timer - each time
    // :: it's called inside single instance, it returns different data
    // :: only lastest ones
    // -------------------------------------------------------------------------
    function getData() {
        $time = time();
        $data = $this->fetch("username, message");
        $this->time = $time;
        return $data;
    }
    // -------------------------------------------------------------------------
    // :: function add new message to databse with current time
    // -------------------------------------------------------------------------
    function newMessage($user, $message) {
        return $this->query("INSERT INTO chat(username, message, timestamp) " .
                            "VALUES (?, ?, strftime('%s','now'))",
                            array($user, $message));
    }
}
{% endhighlight %}

Możemy użyć tej klasy, aby wysłać wiadomość wysłaną AJAX-em, za pomocą metody POST.

{% highlight php startinline=true %}
if (isset($_POST['message']) && isset($_POST['username'])) {
  require_once('Messages.php');

  $messages = new Messages();
  $messages->newMessage($_POST['username'], $_POST['message']);
}
{% endhighlight %}

### Server Side Events w PHP

Teraz pora na główny mechanizm zdarzeń SSE, po stronie serwera

Zdarzenia powinny wyglądać tak:

```
event: Nazwa
id: Numer
data: wiadomość
```

Każda linia powinna być oddzielona znakiem nowej linii (najlepiej użyć `\r\n`).
Każda wiadomość powinna być oddzielona dwoma takimi parami/znakami.
Pole data może być rozbite na wiele linii np.

```
data: to jest widomość
data: wysłana z serwera
```

Typ danych (czyli nagłówek HTTP `Content-Type`) musi być `text/event-stream`.

Poniżej Klasa, która obsługuje ten format danych:

{% highlight php startinline=true %}
// -----------------------------------------------------------------------------
// :: class that send messages using Server Side Events
// -----------------------------------------------------------------------------
class EventStream {
    function __construct($name) {
        ob_start();
        $this->name = $name;
        $this->id = 0;
        $this->setup();
        /* start fresh */
        ob_end_clean();
    }
    // -------------------------------------------------------------------------
    // :: send server side event
    // -------------------------------------------------------------------------
    function send($data) {
        $data = json_encode($data);
        echo "event: {$this->name}\r\nid: {$this->id}\r\ndata: $data\r\n\r\n";
        $this->id++;
    }
    // -------------------------------------------------------------------------
    // :: function that will make php file stream data it will disable any
    // :: buffering that may be added by apache, php or nginx proxy
    // :: ref: https://tinyurl.com/y8yyr6eq (https://www.jeffgeerling.com/blog)
    // -------------------------------------------------------------------------
    private function setup() {
        @ini_set('zlib.output_compression',0);
        @ini_set('implicit_flush',1);
        @ob_end_clean();
        set_time_limit(0);
        header('Content-type: text/event-stream; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        // Setting this header instructs Nginx to disable fastcgi_buffering
        // and disable
        // gzip for this request.
        header('X-Accel-Buffering: no');
    }
}
{% endhighlight %}

Teraz wystarczy tylko ją użyć w pętli, możemy utworzyć nieskończoną pętle,
która będzie nasłuchiwała, czy są nowe wiadomości za pomocą klasy `Messages`:


{% highlight php startinline=true %}
require_once('Messages.php');

$stream = new EventStream("chat");
$messages = new Messages();

echo ":" . str_repeat(" ", 2048) . "\r\n"; // 2 kB padding for IE
echo "retry: 2000\r\n";

while (true) {
    if ($messages->hasData()) {
        foreach ($messages->getData() as $message) {
            $stream->send($message);
        }
    }
    flush();
    if ( connection_aborted() ) break;
    sleep(1);
}
{% endhighlight %}

I to cały kod aplikacji. Kod czatu dostępny na GitHub-ie pod adresem
[https://github.com/jcubic/chat](https://github.com/jcubic/chat)
licencja kodu to MIT.

Demo aplikacji możesz zobaczyć pod linkiem
[https://jcubic.pl/chat/](https://jcubic.pl/chat/)

## Co dalej

Jest to oczywiście bardzo prosty przykład. Co można dodać to np. kolorki dla tych samych
użytkowników, aby działały trzeba by zmienić `textarea` na coś innego (np. zwykły div
z `overflow: auto` lub `scroll`, będzie działał tak samo). Aby dodać kolorki w bazie,
najlepiej dodać nową tabele z użytkownikami (aby nie mieć redundancji w bazie).
W drugiej tabeli warto też dać `username`, a w pierwszej tylko id użytkownika.
Przy dodawaniu wiadomości najpierw trzeba sprawdzić, czy jest to nowy użytkownik.
Jeśli tak generujemy kolorek. Z pomocą przychodzi Stack Overflow
([Generating a random hex color code with PHP](https://stackoverflow.com/a/9901154/387194)).

Potem należy pobierać dane z kolorkami używając złączenia tabel (SQL join).

I na koniec wyświetlić dane użytkowników z kolorkami. Warto też zapisać użytkownika
do localStorage, aby nie pytać go za każdym razem o imię. Można też dodawać komendy,
np. `/nick` może zmienić imię, a `/me` wyświetlić wiadomość kursywą i bez znaku `>`, tak
jak na IRC.

## Podsumowanie

Server-sent events to doskonałe rozwiązanie, gdy nie musimy obsługiwać IE oraz Edge (nowa
wersja na bazie Chromium, będzie już obsługiwała SSE) oraz gdy nie możemy z jakiegoś
powodu używać Web Sockets.

Alternatywą dla Server-sent events jest tzw. long pulling za pomocą AJAX-a, jest to dokładnie
to czym jest Server-sent events, ale bez fajnej abstrakcji, więc wszystko trzeba zrobić samemu.
Jest to dość stara technika, której już się nie używa. Pamięta ktoś
[Comet](https://en.wikipedia.org/wiki/Comet_(programming))?

## Aktualizacja

Musiałem dodać jedną zmianę do kodu (limit znaków wiadomości), bo ktoś wpisał bardzo duży
tekst (same xxxx). Słabo to wyglądało w tej mini apce, więc usunąłem ten wpis z bazy
i ograniczyłem do 400 znaków. Zobacz zmiany na [GitHubie](https://github.com/jcubic/chat).
Jeden z powodów dlaczego istnieją testerzy oprogramowania. Jest to też dowód, na to że często
pierwszy kod ma błędy, nawet gdy jest tak mały jak ten czat.

## Aktualizacja 2

Musiałem dodać jeszcze jedną rzecz. Serwer gdy nie będzie żadnych wiadomości po upływie
limitu 300 sekund zwracał błąd 503. Gdy cokolwiek było wysłane przez HTTP, skrypt po prostu
kończył działanie i nawiązywane było nowe (dzięki SSE). Rozwiązanie, które powinno działać,
to pusta testowa wiadomość na początku, aby nie było pustej odpowiedzi.

### UWAGI
Muszę dodać jeszcze jedną rzecz, którą ominąłem w tekście. W tym przykładowym czacie,
pewnie niektórzy zauważyli, występuje lekkie opóźnienie przy wysyłaniu wiadomości. W gotowym
rozwiązaniu powinno być tak, że wiadomość jest wysyłana do innych użytkowników, a dla osoby,
która wysyła wiadomość, jest ona dodawana w JS, a nie przez serwer. W tej mini apce jest to
zrobione w ten sposób, dla uproszczenia kodu.

I jeszcze druga rzecz, jest nią to, że nazwy użytkowników nie są unikalne, więc jeśli ktoś
będzie filtrował wiadomości po nazwach użytkowników, to może nie dostać wszystkich wiadomości.
Należałoby dodać unikalność imion, aby to uzyskać dobrym pomysłem byłoby dodanie sprawdzania
kto jest online. Można to uzyskać na dwa sposoby, albo robić tzw. heathcheck lub heartbeat,
czyli co kilka sekund wysyłać wiadomość AJAXem, że użytkownik jest online. Lub przy połączeniu
wysyłać jedną wiadomość, a przy zamknięciu przeglądarki drugą. Aby to uzyskać trzeba wysłać zapytanie
do serwera, przy zdarzeniu unload, do tego celu można zastosować API sendBeacon, szczegóły na
[MDN](https://developer.mozilla.org/en-US/docs/Web/API/Navigator/sendBeacon) (to rozwiązanie
nie zadziała w IE, ponieważ ta przeglądarka nie udostępnia tego API, nie można także zastosować
zwykłego AJAX-a ponieważ jest ono przerywane, gdy zamyka się okno przeglądarki).


*[HTTP]: HyperText Transfer Protocol
*[SSE]: Server-sent events
*[HTML]: HyperText Markup Language
*[IRC]: Internet Relay Chat
