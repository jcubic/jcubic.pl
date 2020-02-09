---
layout: post
title:  "Powiadomienia - Push Notifications (aplikacja czatu)"
date:   2020-02-09 20:45:25+0100
categories:
tags: php sqlite javascript ajax http push-notifications service-worker firebase
author: jcubic
description: Jak dodać powiadomienia push notifications za pomocą usługi Firebase.
image:
 url: "/img/mobile-phone-push-notifications.jpg"
 alt: 'Zdjęcie autorstwa **[freestocks.org](https://www.pexels.com/pl-pl/@freestocks?utm_content=attributionCopyText&amp;utm_medium=referral&amp;utm_source=pexels)** z **[Pexels](https://www.pexels.com/pl-pl/zdjecie/dotykac-dzwonic-ekran-dotykowy-google-nexus-4-12829/?utm_content=attributionCopyText&amp;utm_medium=referral&amp;utm_source=pexels)'
 width: 800
 height: 533
---

W tym wpisie dodamy fajną nową funkcje, którą dodałem już jakiś czas temu do
prostej aplikacji czatu, dzięki niej wiem jak ktoś używa tej mini aplikacji.
Jeśli jesteś zainteresowany jak napisać taki czat, polecam najpierw przeczytać artykuł
["Prosty Czat w JavaScript, PHP i SQLite"](/2019/09/prosty-czat-javascript-php-sqlite.html).
Rozwiązanie to używa technologii Server Sent Event (SSE), której można używać, gdy
za jakiegoś powodu nie możemy użyć gniazd (ang. Web Sockets).

Funkcje którą dodamy, to powiadomienia gdy dowolny użytkownik coś napisze na czacie.
Użyjemy powiadomień Push (ang. Push Notifications), service workera oraz
Firebase, aby uprościć sobie życie.

<!-- more -->

## Wprowadzenie

Powiadomienia typu push, jest to nowe API dostępne w przeglądarkach, dzięki któremu
można, dzięki service workerowi (wątku działającym w tle, także po zamknięciu strony)
wysyłać wiadomość z serwera do przeglądarki i dzięki powiadomieniom w przeglądarce
wyświetlić wiadomość użytkownikowi (każdemu kto wyraził na to zgodę).

Osobiście nie lubie tego typu powiadomień, szczególnie że dużo stron nadużywa
tej funkcji i od razy przy wejściu, pyta o pozwolenie na powiadomienia. Nigdy
się nie zgadzam. Tak też myślą programiści przeglądarek i mają zamiar
wyłączyć tą opcje (ukryć), aby nie można było pytać o powiadomienia w ten sposób.

Powiadomienia mają jednak sens, tak jak w naszej aplikacji czatu, gdy damy
możliwość włączenia powiadomień, dzięki czemu użytkownicy będą widzieć gdy
ktoś dołącza i coś pisze oraz mogą zacząć rozmawiać miedzy sobą, a nie tylko
z innym otwartym oknem, aby sprawdzić jak działa czat. Przydatne jest także
w prawdziwej aplikacji gdy ktoś zadaje pytanie i zamyka zakładkę, wtedy gdy
nie ma duży wiadomości dostanie powiadomienie, gdy pojawi się nowa wiadomość.
Ta następna wiadomość najczęściej będzie to odpowiedź na jego pytanie.

## Firebase
Firebase to usługa, której aktualnie właścicielem jest Google, a która udostępnia
ciekawe funkcje m.in. bazę danych czasu rzeczywistego, można ją wykożystać
także do tworzenia czatu, zamiast Server Sent Event (SSE), gdy nie możemy użyć własnych gniazd,
np. na współdzielonym hostingu. Ale dla nas najważniejsze są push notifications,
czyli powiadomienia typu push.

Aby skorzystać z usługi wystarczy zalogować/zarejestrować się używając konta Google.
Następnie należy utworzyć projekt na stronie konsoli firebase,
pod adresem [console.firebase.google.com](https://console.firebase.google.com/).
Gdy mamy już projekt, musimy dodać aplikacje i w ustawieniach
przejść do zakładki Komunikacja w chmurze (ang. Cloud Messaging).

> Jeśli masz problem z utworzeniem aplikacji pozostaje Google, tego typu usługi to
> podstawa i każdy programista powinien umieć z nich korzystać. A jeśli ma problemy
> powinien sam umieć znaleźć odpowiedź w internecie.

Tam możesz pobrać token, który musisz użyć na serwerze, najlepiej zapisać do pliku,
ważne, aby zablokować dostęp z internetu np. za pomocą pliku `.htaccess`.
Można też dodać go do pliku `.gitignore`, aby przez przypadek nie dodać do
repozytorium, jeśli się takiego używa. Polecam do każdej aplikacji.
GitHub udostępnia także darmowe prywatne repozytoria, więc nie ma powodu,
aby nie korzystać, chociaż ja zawsze tworzę publiczne i Open Source.

Aby zainicjować bibliotekę Firebase w JavaScript musimy wkleić kod,
który jest dostępny w zakładce ogólne na stronie ustawień aplikacji.

W moim przypadku to:

{% highlight javascript %}
// Firebase Code
var firebaseConfig = {
    apiKey: "AIzaSyBJguGFPPZXozdkPVpBZNbGMVJ_LTOYuQA",
    authDomain: "jcubic-1500107003772.firebaseapp.com",
    databaseURL: "https://jcubic-1500107003772.firebaseio.com",
    projectId: "jcubic-1500107003772",
    storageBucket: "jcubic-1500107003772.appspot.com",
    messagingSenderId: "1005897028349",
    appId: "1:1005897028349:web:f9f90304397535db17e494"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);
{% endhighlight %}

Użyj swojego kodu, mimo że są to w pewnym sensie klucze, to jednak są one
publiczne bo inaczej nie dałoby się napisać kodu JavaScript.

## Service Worker i Powiadomienia

Aby dodać powiadomienia za pomocą Firebase wystarczy taki kod na głównej stronie:

{% highlight javascript %}
if ('serviceWorker' in navigator) {
   navigator.serviceWorker.register('sw.js', {
       scope: './'
   }).then((registration) => {
       firebase.messaging().useServiceWorker(registration);
       const messaging = firebase.messaging();
       // ask for permissions or use if user already accepted
       if (Notification.permission === "granted") {
           messaging.getToken().then(handleTokens);
       } else {
           Notification.requestPermission().then(function() {
               return messaging.getToken();
           }).then(handleTokens);
       }
       function handleTokens(token) {
           messaging.onTokenRefresh(() => {
              messaging.getToken().then(updateToken);
           });
           updateToken(token);
       }
       // function send AJAX request to register or update token
       function updateToken(token) {
           var data = new FormData();
            data.append('username', username);
            data.append('token', token);
            return fetch('register.php', {
                body: data,
                method: 'POST'
            }).then(r => r.text());
       }
   });
}
{% endhighlight %}

Skrypt `register.php`, będzie służył do zapisania tokenu przeglądarki
w bazie danych, abyśmy mogli wysłać powiadomienie po dodaniu nowej wiadomości.
Główny kod znajduje się w pliku Notifications.php który wygląda tak:

{% highlight php startinline=true %}
require_once('Database.php');
require_once('http.php'); // get and post functions using curl

class Notification {
    public function __construct() {
        $this->db = new Database(); // wrapper over PDO and SQLite
        if (!$this->table_exists('users')) {
            $this->query("CREATE TABLE users(id INTEGER NOT NULL PRIMARY KEY".
                         " AUTOINCREMENT, username VARCHAR(300))");
        }
        if (!$this->table_exists('tokens')) {
            $this->query("CREATE TABLE tokens(userid INTEGER, token VARCHAR" .
                         "(256), FOREIGN KEY (userid) REFERENCES users (id))");
        }
        $this->server_token = file_get_contents('firebase_token');
    }
    // -------------------------------------------------------------------------
    // :: forward every missing method to database object
    // -------------------------------------------------------------------------
    public function __call($name, $args) {
        return call_user_func_array(array($this->db, $name), $args);
    }
    // -------------------------------------------------------------------------
    // :: get id of a user. If user don't exist create one
    // -------------------------------------------------------------------------
    private function get_user_id($username) {
        $ret = $this->query("SELECT * FROM users WHERE username = ?", array($username));
        if (count($ret) == 1) {
            return $ret[0]['id'];
        }
        $this->query("INSERT INTO users(username) values (?)", array($username));
        return $this->lastInsertId();
    }
    // -------------------------------------------------------------------------
    // :: return token for the userid
    // -------------------------------------------------------------------------
    private function token($id) {
        $arr = $this->query("SELECT token FROM tokens WHERE userid = ?", array($id));
        return count($arr) > 0;
    }
    // -------------------------------------------------------------------------
    // :: register new token if there is not already registered
    // -------------------------------------------------------------------------
    public function register($username, $token) {
        $id = $this->get_user_id($username);
        if ($this->token($id)) {
            $this->query("DELETE FROM tokens WHERE userid = ?", array($id));
        }
        $this->query("INSERT INTO tokens(userid, token) VALUES(?, ?)",
                     array($id, $token));
    }
    // -------------------------------------------------------------------------
    // :: send push notification using Firebase to all registered users
    // -------------------------------------------------------------------------
    public function send($username, $message) {
        $rows = $this->query("SELECT * FROM tokens");
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $payload = array(
                    "notification" => array(
                        "title" => "Simple CHAT",
                        "body" => "$username: $message",
                        "icon" => "https://jcubic.pl/chat/icon.png"
                    ),
                    "to" => $row['token']
                );
                $headers = array(
                    "Content-Type: application/json",
                    "Authorization: key=" . $this->server_token
                );
                $res = post(
                    'https://fcm.googleapis.com/fcm/send',
                     json_encode($payload),
                     $headers
                );
                if (__DEBUG__) {
                    $file = fopen('firebase.log', 'a');
                    fwrite($file, $res);
                    fclose($file);
                }
            }
        }
    }
}
{% endhighlight %}

To jest główna część logiki powiadomień po stronie serwera.
Plik `Database.php` zawiera abstrakcje nad PDO do obsługi bazy danych.
Natomiast plik `http.php` zawiera funkcji pomocnicze `get` oraz `post`
wykonujące zapytania HTTP, jak nazwa wskazuje GET oraz POST za pomocą biblioteki
CURL.

zawartość pliku register.php wygląda tak:

{% highlight php startinline=true %}
if (isset($_POST['username']) && isset($_POST['token'])) {
    require_once('Notifications.php');
    $notification = new Notification();

    $notification->register($_POST['username'], $_POST['token']);
}
{% endhighlight %}

Mając rejestracje oraz klasę `Notifications` trzeba ją jeszcze wykorzystać do wysyłania
powiadomień. Do tego celu w klasie Messages wystarczy utworzyć instancje klasy oraz
wywołać jej metodę send gdy wysyłamy wiadomość z czatu.

{% highlight php startinline=true %}
$this->notification = new Notification();

$this->notification->send($user, $message);
{% endhighlight %}

Z klasy `Messages` usunięte zostały także funkcje bazy danych i przeniesione do klasy `Database`.
Jednak dzięki magicznej metodzie `__call` kod działa tak samo. Jeśli czytałeś poprzedni artykuł,
polecam sprawdzenie różnicy (ang. diff) między dwoma gałęziami repozytorium (ang. branch). Link poniżej.

## Co dalej
Powyższa implementacja powiadomień jest moim zdaniem wystarczająca do prawdziwej
aplikacji, brakuje tylko jednej rzeczy, a mianowicie istnieje tylko jeden
token per user, to znaczy że jeśli drugi użytkownik wpisze taką samą nazwę
użytkownika, to skasuje token poprzedniej osobie o takim samym imieniu.
Aby się zabezpieczyć można odróżnić od siebie dwóch użytkowników o tej samej
nazwie (np. generując losową wartość i zapisując w przeglądarce),
ale lepiej po prostu nie pozwalać na taką samą nazwę (zazwyczaj aplikacje
nie pozwalają na dwóch użytkowników o takim samym loginie), można także
mając system rejestracji użytkowników użyć emaila.

W naszej aplikacji, pytanie o powiadomienia, pojawia się od razu po wejściu na stronę,
nie polecam takiego rozwiązanie. Zastosowane tutaj zostały tylko dla uproszczenia.

Polecam dodanie tego typu ikonki za pomocą, której można włączać i wyłączać powiadomienia.

![Push Notification Icon](/img/notifications.svg)

Aby wyłączyć powiadomienia, najlepiej użyć aktualnego tokenu użytkownika, jest do niego
dostęp w przeglądarce i wykonać funkcje `messaging.deleteToken(token)` (szczegóły w
[dokumentacji Firebase](https://firebase.google.com/docs/reference/js/firebase.messaging.Messaging?authuser=00)).
Należy także usunąć token dla użytkownika z bazy danych SQLite.

Ważne jest także, aby zabezpieczyć plik bazy danych przed odczytem z internetu, ponieważ zawiera tokeny
użytkowników. Nie jestem pewien czy atakujący może takie tokeny wykorzystać, ale nie warto ryzykować.

Ponieżej plik `.htaccess`, który zablokuje newralgiczne pliki:

```
RewriteEngine on

RewriteRule firebase_token - [F]
RewriteRule messages.sqlite - [F]
RewriteRule firebase.log - [F]
```

`firebase.log` to dodatkowy plik, w którym zapisywane są odpowiedzi Firebase po stronie serwera.
Logi są tworzone, tylko gdy wartość stałej `__DEBUG__` jest równa true.

## Podsumowanie
Powiadomienia mimo złej sławy, są użyteczne. Warto jednak się zastanowić kiedy
ich użyć. Tak zresztą jest z każdym API, które powstało w jakimś celu.

Kod aplikacji znajduje się w tym samy repo co poprzedni czat,
tylko na branchu notifications:
[github.com/jcubic/chat](https://github.com/jcubic/chat/tree/notifications).

Aby nie zmieniać zachowania poprzedniego czatu, powiadomienia są ukryte i włączają
się, gdy dodamy zmienną Query String `notification` z dowolną wartością.
Czyli wystarczy otworzyć czat poprzez adres:
[jcubic.pl/chat/?notification=x](https://jcubic.pl/chat/?notification=x).
Reszta aplikacji działa tak samo.


