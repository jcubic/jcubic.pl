---
layout: post
title:  "20 rzeczy, które warto znać ucząc się tworzenia stron i aplikacji www"
date:   2019-10-06 11:27:48+0200
categories:
tags: www podstawy internet
author: jcubic
description: O czym warto pamiętać ucząć się tworzenia stron internetowych i aplikacji www. Aplikacje internetowe to nie tylko HTML, CSS oraz JavaScript, ale też wiele informacji dodatkowych, które trzeba znać.
image:
 url: "/img/html-css-javascript-tworzenie-stron.jpg"
 alt: "Zdjęcie z książką o CSS i HTML, laptopem i notatnikiem z napisem: 20 rzeczy, które warto znać ucząc się tworzenia stron i aplikacji www"
 attribution: Źródło [Pixabay](https://pixabay.com/pl/photos/projektowanie-stron-internetowych-2038872/), autor [vanmarciano](https://pixabay.com/pl/users/vanmarciano-1310286/) [licencja Pixabay](https://pixabay.com/pl/service/license/)
 width: 800
 height: 600
---


Zanim zaczniesz naukę tworzenia stron oraz aplikacji internetowych, warto poznać kilka podstawowych
rzeczy. W tym wpisie przedstawię co powinieneś znać. Są to informacje, które możesz poznawać w
trakcie nauki. Zawarte w tym wpisie 20 punktów, omawia zagadnienia, które powinieneś znać, gdy
zajmujesz się tworzeniem stron lub aplikacji internetowych. Oprócz oczywiście HTML, CSS oraz
JavaScript.

<!-- more -->

## 1. Adres IP.

Adres IP jest to adres serwera w internecie, IP jest to podstawa, na której jest on
zbudowany. Np. adres 127.0.0.1 to adres twojego komputera tzw. pętla zwrotna (ang. loopback).

[Więcej o adresach IP na Wikipedii](https://pl.wikipedia.org/wiki/Adres_IP)

## 2. Pakiety oraz TCP/IP.
Nie jest to bardzo potrzebne w tworzeniu stron, ale warto sobie zdawać sprawę z tego jak
działa, przydaje się gdy chcemy zbudować sobie mentalny model, jak naprawdę działa internet.
Główny protokół, czyli sposób w jaki informacja jest przesyłana w internecie, bazuje na
TCP/IP. Jest to protokół, który w skrócie określa, że dane mają być dzielone na pakiety (czyli takie
małe paczki) i przesyłane ścieżkami z jednego adresu IP do drugiego. Ścieżki (ang. route) to
odpowiednie komputery (serwery) lub inne urządzanie, które także mają swój adres IP,
np. routery. Każdy pakiet może podróżować innymi ścieżkami. Dla nas najważniejsze jest to, że TCP/IP
zapewnia, że wszystko to co się za jego pomocą wyśle dotrze do adresata. Czyli najpierw dane są
dzielone na części, wysyłane różnymi ścieżkami (lub tą samą ścieżką) do adresata i tam składane w
całość. Gdy jakiś pakiet się zagubi zostanie wysłany ponownie.

[Więcej o TCP/IP na Wikipedii](https://pl.wikipedia.org/wiki/Model_TCP/IP).

## 3. Domena

Jest to nazwa serwera, który identyfikuje go w internecie.  Domeny zarządzane są przez system DNS
(ang. Domain Name System), służy on do tego, aby zamieniać adresy IP serwera na przyjazne nazwy.
Nie musisz pamiętać jaki ma adres IP strona www Google tylko używasz nazwy google.pl. Dzięki temu jedna
domena może wskazywać na kilka adresów IP. Można też je zmienić i użytkownicy tego nie zauważą.
Serwer lokalny np. jest nazywany localhost, ale ta nazwa nie jest zapisana w systemie DNS, tylko w
systemie operacyjnym. Możesz sobie np. dodać do własnego użytku, różne nazwy dla localhost,
np. dev.domena.pl. Lokalne nazwy będą nadpisywały te z systemu DNS. Więcej o
[DNS na Wikipedii](https://pl.wikipedia.org/wiki/Domain_Name_System).

## 4. Serwer

Istnieją dwa rodzaje serwerów. Fizyczna maszyna oraz program, który nasłuchuje na odpowiednie dane
przesyłane przez TCP/IP i zwraca odpowiednio przetworzone dane. Może to być np. serwer www, który
odbiera informacje o tym, jakie strony otworzyć (zazwyczaj z przeglądarki) i zwraca zawartość
strony.

Serwery komunikują się za pomocą specjalnych protokołów, które są szczegółowo opisane w
[dokumentach RFC](https://pl.wikipedia.org/wiki/Request_for_Comments). Ale można np. też napisać
swój własny protokół gdy np. piszemy aplikacje klient-serwer, możemy przesyłać przez sieć cokolwiek
nam się podoba. Np. serwery www komunikują się za pomocą protokołu HTTP (ang. Hypertext Transfer
Protocol). Natomiast poczta jest wysyłana protokołem SMTP, a odbierana za pomocą IMAP lub POP3. Jeśli
konfigurowałeś program pocztowy, to pewnie pamiętasz, że musiałeś znać adresy tych serwerów.

Istnieją także aplikacje serwerowe, których protokół jest tajny.  Można się tylko domyślać jak
działają. Przykładem mogą być programy takie jak Skype czy WhatsApp.

Najprostszym przykładem własnego serwera, jest echo, czyli wszystko co jest wysyłane do tego serwera,
będzie wysyłane z powrotem.

## 5. Klient

Jest to aplikacja, która znajduje się po drugiej stronie serwera. Serwer nasłuchuje
połączeń. Natomiast klient, to właśnie to na co czeka serwer. Klientem jest np. przeglądarka
internetowa, na naszym komputerze lub aplikacja do obsługi poczty. Zazwyczaj w jednym momencie
do serwera może być "podpiętych" kilka klientów. Gdy jest ich bardzo dużo może dojść do przeciążenia
usługi. Zdarza się, że wykonywane jest to celowo, nazywa się to wtedy atakiem
[DoS](https://pl.wikipedia.org/wiki/DoS) lub [DDoS](https://pl.wikipedia.org/wiki/DDoS).

Aplikacje składające się z serwera i klientów nazywane są aplikacjami typu klient-serwer.

## 6. Hosting

Jest to usługa, która udostępnia miejsce na dysku na twoją stronę. Jest wiele takich firm,
udostępniających różne funkcjonalności. Ja korzystam z firmy
[Atthost](https://ref.atthost.pl/?id=10912), ze względu na liczbę funkcji jakie udostępnia.
Masz wszystko i tylko płacisz odpowiednio mniej lub więcej za ilość miejsca jaką chcesz mieć.  Ma
praktycznie wszystko czego będziesz potrzebować, nawet gdy będziesz pisać zaawansowane aplikacje
internetowe. Więcej mają już tylko usługi VPS, ale to tylko dla zaawansowanych.  O aplikacjach
internetowych, będę pisał w jednym z następnym artykułów.

Funkcje jakie ma [Atthost](https://ref.atthost.pl/?id=10912) to SSH, darmowy SSL, SSD czyli szybie
dyski bez ruchomych części, FTP, prosto konfigurowalne aplikacje w Node.js, Ruby on Rails, Python - Django czy Flask,
(wymaga to więcej pracy, ale jak chcesz, to możesz nawet napisać aplikacje w C++, masz kompilator na
serwerze, więc możesz sobie nawet zainstalować i używać języka LIPS np. przez aplikacje Guile,
jeszcze nie próbowałem. Taka aplikacja to prawdziwy serwer www, a nie tylko skrypt CGI) możesz mieć
też u nich swoją domenę. Masz 14 Dni na testy. Jak kupisz hosting używając powyższego linka, to
dostane jakieś grosze, dzięki którym będę miał fundusze na utrzymanie tej strony.

Oprócz zwykłych hostingów istnieją także usługi nazywane zbiorczo
[Chmurą](https://pl.wikipedia.org/wiki/Chmura_obliczeniowa) (chodzi głównie o PaaS), służą one
jednak do większych aplikacji. Dzięki nim można obsłużyć miliony użytkowników, można oczywiście też
używać mniej. Hosting jest o wiele prostszy.  Służy głównie do trzymania (hostowania) stron www lub
mniejszych aplikacji.

Ale nawet, gdy piszesz coś większego, warto skorzystać ze zwykłego hostingu (szczególnie, gdy już na
przykład taki używasz do swojej strony internetowej) i tylko gdy jest na maksa przeciążony, szukać
innych rozwiązać. Może się zdarzyć, że dla twojej aplikacji, zwykły hosting wystarczy.


## 7. SSH oraz FTP

Są to dwa protokoły, z których prawdopodobnie będziesz korzystać, gdy będziesz miał hosting
www. [Atthost](https://ref.atthost.pl/?id=10912) udostępnia oba. SSH czasami nie jest dostępny w
usługach hostingowych, a dzięki niemu można się "przenieść" do systemu operacyjnego na serwerze
(czyli GNU/Linuxa i wykonywać tam polecenia, co jest bardzo użyteczne). Na poprzednim hostingu tego
nie miałem i napisałem specjalnie aplikację, która udostępnia wiersz poleceń o nazwie
[Leash](https://leash.jcubic.pl/), jeśli masz już hosting, który nie ma SSH, możesz z niej skorzystać
lub przejść do Atthost.

Protokół SSH jest następcą innego protokołu o nazwie Telnet, który działał dokładnie tak samo,
nie był jednak szyfrowany. Co ciekawe za pomocą programu telnet (dostępny także na Windowsie)
można wykonać zapytanie http łącząc się z portem 80. Nie można jednak się pomylić i skasować tego
co się wysłało, ponieważ Telenet wysyła znak po znaku (możesz np. wykonać `telnet jcubic.pl 80`
i wysłać zapytanie HTTP, czytaj dalej jak wygląda takie zapytanie).

Jeśli korzystasz np. Windowsa 7, zainteresuj się programem [PuTTY](https://pl.wikipedia.org/wiki/PuTTY).

FTP natomiast to protokół do przesyłania plików. Możesz np. użyć fajnego prostego programu Open
Source [Filezilla](https://filezilla-project.org/) do przesyłania plików na serwer. W przeglądarce
Firefox był też fajny dodatek o nazwie [FireFTP](http://fireftp.net/) (był ponieważ kiedyś używałem,
ale teraz rzadko korzystam z tej przeglądarki). Jak widzisz ze strony, autor udostępnia także
[FireSSH](http://firessh.net/). Jeśli korzystasz z Firefoxa to warto skorzystać, szczególnie gdy
korzystasz ze starszego Windowsa (przed Windows 10) ponieważ tam masz dostęp do prawdziwego Linuxa (WSL).

Więcej o [SSH](https://pl.wikipedia.org/wiki/Secure_Shell) i
[FTP](https://pl.wikipedia.org/wiki/Protok%C3%B3%C5%82_transferu_plik%C3%B3w) przeczytasz na Wikipedii.

Warto dodać, że jest także narzędzie [SCP](https://pl.wikipedia.org/wiki/Secure_copy) do szyfrowanego
kopiowania plików poprzez SSH. Oraz protokół
[SFTP](https://pl.wikipedia.org/wiki/SSH_File_Transfer_Protocol).

## 8. Podstawy GNU/Linuxa

Linux to super narzędzie programistyczne, jest o wiele lepszy niż MacOS oraz Windows. Szczególnie
chodzi i wiersz poleceń, za pomocą którego można w prosty sposób, wykonać operacje, które wymagają
specjalnych aplikacji w środowisku Windows. Więc musisz sam napisać coś takiego, albo szukać już
istniejącego rozwiązania.  Jest to także system samo dokumentujący, który dzięki temu że jest Open
Source i składa się z wielu oddzielnych klocków (osobnych programów), można studiować małymi
kroczami i prosto modyfikować (coś takiego by było nie możliwe w Windows czy MacOS nawet jakby kod
był udostępniony). Samo używanie systemu zachęca Cię do nauki programowania i poznawania
systemu. Chociaż MacOS to też unix, a Windows 10 udostępnia WSL, nic nie zastąpi prawdziwego systemu
GNU Linux.

Konfiguracja systemu Linux jest o wiele bardziej sensowna, wszystko opiera się na plikach tekstowych.
Jest też o wiele więcej informacji w internecie o tym systemie i łatwiej znaleźć pomoc, mimo że
system wydaje się trudniejszy w obsłudze. Ten paradoks wynika z tego że używając Linuxa cały czas
się uczysz. Korzystając z Windowsa możesz być tylko zwykłym użytkownikiem, nie zachęca on do
eksploracji. Konfiguracja tego systemu nie należy do najprzyjemniejszych czynności.

Jeśli masz Windowsa 10 koniecznie zainstaluj WSL (czyli Windows Subsystem for Linux). Ważne jest to
także, gdy zainteresujesz się [Dockerem](https://pl.wikipedia.org/wiki/Docker_(oprogramowanie)). W
skrócie jest to środowisko, do umieszczania aplikacji w wirtualnym systemie Linux, na dowolnym
systemie. Przyda się także do obsługi systemu kontroli wersji
[GIT](https://pl.wikipedia.org/wiki/Git_(oprogramowanie)). Oba narzędzia raczej dla programistów,
albo dla tych, którzy chcą się rozwijać i uczyć nowych umiejętności.

Tutaj krótki, kilku częściowy, kurs obsługi wiersza poleceń, to samo będzie w Windows (WSL), Linux
oraz MacOSX.

<iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?list=PLjHmWifVUNMI9WNqkJhDnmMYWi9GIzYTH"
  frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
  allowfullscreen></iframe>


## 9. Port

Port jest to taka furtka, przez którą przechodzą pakiety TCP/IP między komputerami w sieci. Jest on
określany jako liczba od 0 do 65535. Np. gdy otwierasz stronę www w przeglądarce, to pobierasz ją z
portu 80, na którym nasłuchuje serwer www (lub 443 dla https).  Serwery aplikacji mają zazwyczaj
domyśle porty. Gdy korzysta się z HTTP oraz HTTPS, nie trzeba podawać numeru portu, ale np. nic nie
stoi na przeszkodzie aby serwer www był "schowany" pod innym portem. Większość protokołów posiada
domyślny port, na który zazwyczaj nasłuchuje.  Np. w hostingu Atthost serwer SSH nasłuchuje na innym
porcie, dlatego trzeba go podawać. Jak używać portów w adresie w następnym punkcie o adresach
URL. Więcej o portach przeczytasz na
[Wikipedii](https://pl.wikipedia.org/wiki/Port_protoko%C5%82u).


## 10. Adres URL

Jest to adres strony www a dokładnie, pliku na serwerze www. Może to też nie być faktyczny fizyczny
plik na dysku np. gdy mamy blog Wordpress to adresy URL mogą wskazywać wpisy na blogu, które
pobierane są z bazy danych. Dokładnie więc mówiąc, adres URL wskazuje na konkretny zasób na serwerze
którego adres URL jest jego identyfikatorem. Przykładowy adres URL wygląda tak
[https://jcubic.pl/search.php?q=serwer](https://jcubic.pl/search.php?q=serwer), jest to coś co
będziesz miał w pasku adresu przeglądarki, gdy wyszukasz słowo "serwer" na tej stronie.

Jeśli serwer www byłby np. na porcie 8080 pod moim adresem domeny, musiałbym otworzyć stronę
http://jcubic.pl:8080, dla połączenia nieszyfrowanego. Można mieć więcej niż jeden serwer www
na jednej maszynie, każdy na innym porcie. Często się zdarza, że port 80 jest zajęty i odpala
się drugi np. na porcie 8080 lub 3000, jeśli robisz to lokalnie, to adres takiego serwera będzie
wyglądał tak http://localhost:3000.

Szczegóły na temat adresów
[URL oraz URI na Wikipedii](https://pl.wikipedia.org/wiki/Uniform_Resource_Locator).

## 11. Protokół HTTP

Jest to najważniejszy protokół w sieci www. Dzięki niemu przesyłane są strony HTML oraz inne pliki,
z serwera do przeglądarki internetowej. HTTP to protokół tekstowy, który jest bezpołączeniowy,
tzn. nie ma połączenia między serwerem a klientem. Wysyłane jest pytanie i potem jest zwracana
odpowiedź, po tym połączenie się kończy.  Jest to także protokół bezstanowy, tzn. że serwer nie
posiada stanu tego co było wcześniej.  Istnieją jednak pewne metody i techniki, aby utrzymywać taki
san i połączenie, ale jest to tylko pewne nadużycie tego protokołu. Jest ono jednak bardzo często
wykorzystywane. (jeśli jesteś zainteresowany tym zagadnieniem to zobacz
[mój wpis o SSE (ang. Server Sent Events)](/2019/09/prosty-czat-javascript-php-sqlite.html) oraz
niżej informacje o ciasteczkach).

Poniżej proste zapytanie HTT, dla przykładu wyszukiwania słowa "serwer" na mojej stronie:

```
GET /search.php?q=serwer HTTP/1.1
Host: jcubic.pl
```

i tyle wystarczy, aby wysłać zapytanie HTTP. Jest to minimalne zapytanie, aby otrzymać jakąś
odpowiedź.  Pierwsza linia jest specjalna, na którą składa się słowo kluczowe, o jakie zapytanie
chodzi. Może to być np. GET, POST, HEAD lub PUT. Potem ścieżka do zasobu i wersja protokołu. Dalej są
nagłówki HTTP, tutaj tylko jeden (przeglądarka zazwyczaj wysyła więcej różnych nagłówków).  Nagłówek
host jest bardzo ważny, gdy np. na jednym adresie IP, jest więcej niż jedna strona internetowa. Ma to
miejsce na współdzielonym hostingu (ang. shared hosting), gdzie pod jednym adresem może być wiele
stron internetowych (stąd nazwa współdzielony).

Musisz także pamiętać, że serwer www nie wie nic co DNS, czyli nazwach domen. Słucha on po prostu na
porcie. Dostaje poprzez TCP/IP zwykły tekst, który następnie przetwarza i odsyła odpowiedź na inny
adres IP. Czyli najczęściej do przeglądarki (może to być także skrypt, czyli program nazywany po
angielsku [scraper](https://en.wikipedia.org/wiki/Web_scraping) lub bot np. ten od Google, który
służy do indeksowania stron).

Przykładowa odpowiedź serwera (na przykładzie mojej strony), gdy powyższy tekst jest przesłany na
port 80.

```
HTTP/1.1 302 Found
Server: nginx
Date: Sat, 05 Oct 2019 12:20:21 GMT
Content-Type: text/html; charset=iso-8859-1
Content-Length: 221
Connection: keep-alive
Location: https://jcubic.pl/search.php?q=serwer
Cache-Control: max-age=0
Expires: Sat, 05 Oct 2019 12:20:21 GMT

<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>302 Found</title>
</head><body>
<h1>Found</h1>
<p>The document has moved <a href="https://jcubic.pl/search.php?q=serwer">here</a>.</p>
</body></html>
```

Jest to po prostu przekierowanie, ponieważ moja strona działa na HTTPS, o czym dalej. Mamy pierwszą
linię, która mówi, że kod odpowiedzi to 302, czyli przekierowanie oraz wersja protokołu. Dalej są
nagłówki HTTP odpowiedzi, ważny jest nagłówek Location. Gdy przeglądarka zobaczy odpowiedź 302,
poszuka nagłówka location i otworzy tą stronę (czyli przekieruje na ten adres). Za nagłówkami
znajduje się jedna pusta linia (czyli dwa znaki końca wiersza \n\n) i strona HTML. Jest ona po to,
aby użytkownik coś zobaczył, gdy jego przeglądarka nie zrozumie, że jest to przekierowanie. Wtedy
użytkownik będzie mógł sam kliknąć na link, który go przekieruje na prawidłową stronę.

[Więcej o HTTP na Wikipedii](https://pl.wikipedia.org/wiki/Hypertext_Transfer_Protocol).

## 12. Ciasteczka

Ciasteczka (ang. Cookies) jest to specjalny mechanizm, dodany do protokołu HTTP.  Dzięki nim można
zapisać stan między poszczególnymi zapytaniami HTTP, dzięki mechanizmom sesji (stan nie jest zawarty
w samym protokole tylko w aplikacji po stronie serwera, która używa HTTP).  Można np. napisać
aplikacje, gdzie można się zalogować i każda następna strona będzie pamiętać, że jesteś
zalogowany. Jest to zazwyczaj realizowane dzięki ciasteczkom.  Ciasteczko jest to po prostu pewien
tekst wysyłany do przeglądarki, który jest za każdym nowym zapytaniem zwracany do serwera. W
ciasteczkach można zapisywać informacje, w formacie klucz wartość. Np. można zapisać, że
id_użytkownika to 111234567.

Wiedza o ciasteczka przydaje się głównie przy tworzeniu aplikacji internetowych.
Więcej na ten temat ciasteczek na [Wikipedii](https://pl.wikipedia.org/wiki/HTTP_cookie).
Warto dodać, że istnieją także inne metody zapisywania informacji w przeglądarce np.
localStorage czy IndexedDB. Ciasteczka są często źródłem problemów np. w Unii Europejskiej
strony muszą wyświetlać informacje o używaniu ciasteczek. Mogą także prowadzić do problemów
z bezpieczeństwem, gdy używa się ich w niepoprawny sposób. To samo tyczy się prawie każdej
technologii internetowej. O niebezpieczeństwach przeczytasz w jednym z kolejnych punktów.

## 13. SSL oraz TLS

TLS (ang. Transport Layer Security), jest to rozszerzenie protokołu SSL (ang. Secure Socket Layer)
stworzonego przez firmę Netscape (producentem jednej z pierwszych przeglądarek internetowych,
przodka Firefoxa), dzisiaj stosowany jest głównie TLS, ponieważ jest to nowsza wersja. A służy on do
szyfrowania danych, które są zazwyczaj tekstowe i jawne, więc każdy może takie dane podsłuchać,
gdy nie stosuje się szyfrowania.

Jeśli czytałeś punkt na temat protokołu TCP/IP to pisałem, że pakiety wędrują miedzy
urządzeniami. Wystarczy więc, że jakaś osoba będzie nasłuchiwać na takim urządzeniu. Jest to
tzw. atak MITM (ang. Man In The Middle). Dzięki takiemu atakowi, ktoś może np. odczytać twoje hasło
lub zmienić dane, które trafiają na serwer. Dlatego banki i inne ważne strony korzystają z HTTPS,
czyli szyfrowanego połączenia. Praktycznie każda strona, która ma system logowania powinna
używać HTTPS. Co jest tak na prawdę bardzo proste i tanie (na Atthost masz za darmo certyfikat SSL
dzięki usłudze [Let’s Encrypt](https://pl.wikipedia.org/wiki/Let%E2%80%99s_Encrypt)).

Więcej o TLS na
[Wikipedii](https://pl.wikipedia.org/wiki/Transport_Layer_Security).

Protokóły SSH i SFTP oraz narzędzie SCP korzystają z takiego szyfrowania.

## 14. Błędy aplikacji internetowych

Jest masę błędów, które możesz wprowadzić nieświadomie do aplikacji, a które mogą być wykorzystywane
przez hakerów/krakerów. Jednym z ataków jest wspomniany wcześniej MITM. Więcej informacji przeczytasz
w dłuższym artykule o
[dziurach w aplikacjach i ich wykorzystaniu przez hakerów/krakerów](/2018/01/bledy-aplikacji-internetowych.html).

## 15. Konsola Google Chrome oraz stare pokaż źródło (ang. view source)

Gdy uczysz się tworzyć strony internetowe oraz aplikacje webowe, ważne jest, aby móc sprawdzić jak coś
zostało zrobione, gdy wejdziesz na fajną stronę lub aplikacje. Kiedyś do tego celu używało się view
source czyli pokaż źródło (jest to opcja dostępna z menu kontekstowego, gdy klikniemy prawym
klawiszem myszy gdzieś na stronie). Druga bardziej rozwinięta funkcja to narzędzia deweloperskie
(ang. dev tools). Jest to zestaw narzędzi, dzięki którym można sprawdzić, jak wygląda strona lub
aplikacja internetowa, w danym momencie. Jest to ważne, ponieważ dzięki JavaScript, strona może być
zupełnie inna niż jej źródło.

Dev tools jest szczególnie ważne z powodu tego, że często strony www są zaciemnione
(ang. obfuscated). Robi się to głównie dlatego, aby strona mniej ważyła (miała mniej znaków, dzięki
czemu ładowała się szybciej).  Może być też zastosowane, gdy właściciel strony nie chce, aby ktoś
wiedział jak coś zostało zrobione.  Można tylko utrudnić zrozumienie działania strony, całkowicie
jest to niemożliwe.  Jeśli jesteś ciekawy na ile jest to możliwe możesz zobaczyć stronę hakera
Samy'ego Kamkar'a [https://samy.pl/](https://samy.pl/).  Ma on swoją stronę na
[angielskiej Wikipedii](https://en.wikipedia.org/wiki/Samy_Kamkar), zajrzyj jeśli jesteś
zainteresowany (szczególnie ciekawy jest wątek ze stroną MySpace i jak stał się znajomym miliona
ludzi, przez co musieli zamknąć stronę, aby naprawić problem).

Aby dostać się do tych narzędzi, należy kliknąć prawym klawiszem myszy i wybrać opcje "zbadaj" lub
"zbadaj element" (ang. inspect element) albo wcisnąć klawisz F12. W przypadku przeglądarki
Safari na MacOSX, trzeba taką opcję włączyć, ponieważ jest domyślenie ukryta. Poszukaj sam
jeśli masz tą przeglądarkę. Szukanie informacji to jeszcze jedna ważna umiejętność.

## 16. Plik .htaccess

Warto by też wspomnieć o plikach `.htaccess`. Jest to sposób konfiguracji serwera Apache,
popularnego serwera www, który zazwyczaj jest dostępny na współdzielonych hostingach.  Za pomocą
pliku `.htaccess` można np. dodać przekierowania, np. gdy przenosimy stronę z jednego adresu na
inny, lub gdy np. zmieniamy domenę i chcemy, aby nowa miała ten sam ranking w Google.  Może się też
zdarzyć, że np. wewnętrzne adresy strony się zmieniły, np. przenieśliśmy plik w inne miejsce lub
użyjemy innej aplikacji, która ma inny format adresów URL.

[Więcej o pliku .htaccess na Wikipedii](https://pl.wikipedia.org/wiki/.htaccess)

## 17. SEO oraz pozycjonowanie stron

Warto znać podstawy SEO - jak konstruować strony internetowe, aby były dobrze zindeksowane przez
Google.

Poniżej dwa filmy, pierwszy wyjaśniający, drugi jak optymalizować stronę czyli o SEO.

<iframe width="560" height="315" src="https://www.youtube.com/embed/j2xP4r0evWI"
  frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
  allowfullscreen>
</iframe>

<iframe width="560" height="315" src="https://www.youtube.com/embed/bW48kAIbSms"
  frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
  allowfullscreen>
</iframe>


## 18. Semantyczny HTML oraz dostępność stron internetowych

Jeśli znasz już HTML to powinieneś też zaznajomić się z semantyczną strukturą strony.

Polecam kurs ["Semantyczny blog w HTML5"](https://tutorials.comandeer.pl/html5-blog.html)
autorstwa Tomasza "Comandeer" Jakuta.

O dostępności, czyli accessibility (czasami zapisywane jako a11y), nie wiem za dużo, więc musisz
sam poszukać na ten temat informacji. Jest to coś o czym sam muszę jeszcze trochę poczytać.
Tworzenie stron internetowych i aplikacji webowych to ciągła nauka.


## 19. Web Design oraz UX przy tworzeniu stron i aplikacji internetowych

Tworząc strony internetowe oraz aplikacje samemu, warto umieć obsługiwać jakiś program graficzny
oraz mieć podstawową wiedzę na temat projektowania graficznego. Polecam darmowy program (jest to
program Open Source) o nazwie [Inkscape](https://inkscape.org/). Jest to wolna alternatywa dla
własnościowego i płatnego programu Ilustrator od Adobe czy programu Corel Draw. Jest zupełnie
darmowy i dostępny na Windowsa, Linuxa i MacOS. Program do grafiki wektorowej jest moim zdaniem o
wiele lepszy niż do grafiki rastrowej taki jak Adobe Photoshop czy GIMP.

Poniżej krótki kurs Inkscape'a (kilka filmów)

<iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?list=PLffA1rZ1DsbpZOKaQcNuFCqU0VBYrhvN2"
  frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
  allowfullscreen></iframe>

Polecam też książkę

<a href="https://helion.pl/view/12418M/deshak.htm" target="_blank"
   title="Design dla hakerów. Sekrety genialnych projektów">
   <img src="https://static01.helion.com.pl/global/okladki/326x466/deshak.jpg"
   alt="Okładka książki: Design dla hakerów. Sekrety genialnych projektów"/>
</a>

Niestety w księgarni Helion tylko ebook, prawdopodobnie nikt z drukowaną kopią się nie rozstaje,
jest tak dobra.

Tak jak w przypadku dostępności, jeśli chodzi to UX to mam tylko intuicję i zdrowy rozsądek.
Jest to coś na temat czego także powinienem trochę poczytać. Dobrą praktyką jest niezaskakiwanie
użytkowników, jeśli coś już gdzieś działa i ludzie są do tego przyzwyczajeni warto z tego skorzystać.
Druga zasada jaką się kieruje to prostota, którą można zastosować w każdym aspekcie tworzenia
stron i aplikacji. Prostsze jest prawie zawsze lepsze, co nie znaczy wcale, że łatwiejsze do
utworzenia. Czasami stworzenie czegoś prostego wymaga więcej pracy.

Przytoczę tutaj cytat:

> Szanowny panie hrabio, przepraszam za tak długi list, ale nie miałem czasu, żeby sformułować
> go krócej.

Według [cytaty.info](https://www.cytaty.info/cytat/johannwolfganggoethenapisal.htm) autorem
był J. W. Goethe.


## 20. Jak zadawać pytania

Na koniec bardzo ważna umiejętność. Tworząc strony i aplikacje internetowe, jest bardzo
prawdopodobne, że będziesz miał jakieś problemy. Wtedy możesz szukać pomocy. Gdy nie znajdziesz
odpowiedzi w Google, będziesz musiał zadać gdzieś pytanie. Dobrym miejscem są np. grupy na Facebooku lub
strona [StackOverflow](https://stackoverflow.com/), która często pojawia się jako pierwsza, w
wynikach wyszukiwania. Musisz nauczyć się poprawnego identyfikowania problemu, który masz i
odpowiedniego jego nazywania. Tutaj pomocna jest tylko praktyka. Jest to powiązane z umiejętnością
dzielenia problemu na części.

Poniżej dwie strony, które warto przeczytać przed zadaniem pytania:

* [Jak mądrze zadawać pytania](http://rtfm.killfile.pl/) tłumaczenie eseju hakera
  [Erica S. Raymonda](https://pl.wikipedia.org/wiki/Eric_Raymond), polecam też jego książkę
  UNIX. Sztuka programowania. (nie koniecznie dla osób, które programują na Unix-a lub Linux-a)

<a href="https://helion.pl/view/12418M/unszpr.htm" target="_blank" title="UNIX. Sztuka programowania">
  <img src="https://static01.helion.com.pl/global/okladki/326x466/unszpr.jpg"
       alt="Okładka książki: UNIX. Sztuka programowania"/>
</a>

* [How do I ask a good question?](https://stackoverflow.com/help/how-to-ask) - FAQ StackOveflow.

Jeśli założysz konto na StackOverflow, to warto także odpowiadać na pytania. Można w ten sposób się
wiele nauczyć. Szczególnie, gdy do końca nie wiesz, jak coś zrobić. Szukasz kilku rzeczy i tworzysz
rozwiązanie. Jest to dobra praktyka, gdy już znasz podstawy tworzenia stron, albo podstawy
programowania aplikacji i nie wiesz co dalej. Konto na SO raz przydało mi się nawet podczas
rekrutacji. Ja zatrudniając kogoś na pewno bym poprosił o link do konta na SO, jeśli kandydat takie ma
(zaraz za linkiem do GitHuba lub innego hostingu GIT-a) i byłby to czynnik, który w jakimś stopniu
by zadecydował kogo bym zatrudnił.

Znasz jeszcze coś, czego warto by się nauczyć, gdy poznajesz sposoby tworzenia stron internetowych
i aplikacji webowych. Napisz w komentarzu.

*[PaaS]: Platform as a Service
*[DoS]: Denial Of Service
*[DDoS]: Distributed Denial Of Service
*[FAQ]: Frequently Asked Questions
