---
layout: post
title:  "10 błędów aplikacji www wykorzystanych przez Hakerów"
date:   2017-12-25 16:30:22+0100
categories:
tags:  hacking www
author: jcubic
description: Najczęstsze błędy aplikacji internetowych, które mogą być wykorzystane przez Hackerów a raczej Crackerów oraz jak się przed nimi bronić.
---

W tym artykule przedstawię najczęstsze błędy aplikacji internetowych, które mogą być wykorzystane przez Hackerów a raczej Crackerów oraz jak się przed nimi bronić

<!-- more -->

## 1. Cross-Site Scripting

XSS czyli podatność wstrzykiwania zewnętrznego polega na tym, że hacker/cracker wymusza na naszej aplikacji wykonanie zapytania
http do zewnętrznego serwera (wcześniej przez siebie przygotowanego), które zawiera zastrzeżone dane np. ciasteczka lub hasło.
Może to być kod JavaScript ale również [CSS](https://typeofweb.com/2017/12/15/hackowanie-css/) albo XML.

Istnieją jego dwa rodzaje:

## Reflected, czyli odwzorowany
Polega na tym że w zapytaniu np. w parametrze zapytania HTTP typu GET możemy wstawić kod JavaScript, który zostanie odwzorowany
w wygenerowanym kodzie. Nie musi to być także inne zapytanie np. POST wtedy atakujący może utworzyć stronę na innym serwerze,
która wyśle formularz do atakowanej stronie. Wtedy url takiej strony musiałby zostać przesłany ofiarze np. w emailu. Aby taki
url wyglądał bezpieczniej atakujący może użyć np. przekierowania które opisane będzie dalej w tym artykule lub skrócić url za
pomocą jednej z wielu usług np. [bitly.com/](https://bitly.com/).

przykładem może być zapytanie `http://example.com/search?term=<script>alert('xss')</script>` jeśli aplikacja wyświetli stronę
404 z wyrażeniem term wstawionym do kodu html strony, będzie to XSS typy odwzorowanego.

### Zapisany (ang. Stored)
Polega on na tym że nasz kod jest zapisywany np. bazie danych i za każdym razem, gdy jakiś użytkownik otworzy stronę wykona
się kod, wstrzyknięty przez agresora.

### Jak się zabezpieczyć
Aby zapobiec atakom tylko XSS, należy filtrować dane od użytkownika oraz bazy danych lub je odpowiednio formatować (ang. escape),
jeśli są wstawiane do naszej strony. Możemy np. skorzystać z jakieś frameworka, który zrobi to za nas. Często systemy szablonów
nie pozwalają na wstawianie kodu html ze zmiennej i trzeba użyć specjalnej komendy lub filtra by była taka możliwość.

### Co dalej
Polecam dwa artykuły, w języku angielskim, na stronach OWASP
(XSS Filter Evasion Cheat Sheet)[https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet] oraz
[XSS (Cross Site Scripting) Prevention Cheat Sheet](https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet),
czyli odpowiednio obchodzenie i zabezpieczanie aplikacji.

## 2. SQL Injection

Ta podatność polega na tym, że jeśli używamy danych od użytkownika w zapytaniu SQL bez odpowiedniego ich formatowania,
złośliwy użytkownik może wstrzyknąć swój kod SQL który wykona się razem z tym naszym.

Przykład

Nasza aplikacja może wywoływać takie zapytanie (kod php):

{% highlight php %}
$query = "SELECT * FROM users WHERE username = '$username' and password = '$password'"
{% endhighlight %}

jeśli zmienna `$username` lub `$password` pochodzi od użytkownika, np. z URLa lub zapytania POST, to jest możliwość przekazać
w niej, zamiast hasła, kod SQL np. `' or 1=1;--`. Wynikowe zapytanie po wstawieniu naszego hasła będzie wyglądało następująco:

{% highlight sql %}
SELECT * FROM users WHERE username = 'name' and password = '' or 1=1;--'
{% endhighlight %}

To zapytanie zwróci dane danego użytkownika, mimo ze hasło jest pustym ciągiem znaków. (znaki `;--` oznaczają zakończenie
zapytania oraz zakomentowanie tego co jest za nim, czyli znaku cytatu z oryginalnego zapytania).

Istnieją dwa rodzaje podatności:

### Zwykłe
Wyświetla dane z zapytania na stronie. Za pomocą niego można wykraść wszystkie dane zapisane w bazie danych (np. poprzez zapytanie
UNION SELECT, które pozwala na dodanie, danych z innej tabeli, do zapytania, do którego wstrzykiwany jest kod SQL) ale także hasło
do bazy danych, jeśli aplikacja korzysta z konta root bazy. Jeśli aplikacja umożliwia wykonywanie wielokrotnych zapytań albo gdy
mamy hasło do bazy danych atakujący może dodać do bazy dowolne dane np. jakiś złośliwy kod, gdy aplikacja jest podatna na XSS.

### Ślepe (ang. blind)
Natomiast ślepe może wyświetlać dwa rodzaje stron, np. pustą i z danymi wtedy atakujący może wstrzyknąć zapytanie, które zwraca
prawdę albo fałsz (można do tego celu użyć `CASE..WHEN`) albo prawdę lub zwracać wyjątek np za pomocą `CASE..WHEN` gdzie `ELSE`
wywołuje błąd (można wywołać `convert(int, 'x')` w przypadku bazy MSSQL). Aplikacja może także nie zwracać nic, ale nadal być
podatna na SQL injection, można wtedy np. do zapytania gdy zwraca prawdę dodać opóźnienie i sprawdzać czas jaki zajmuje zapytanie.

W atakach SQL injection może także wykorzystywać błędy zwracane przez aplikacje (tzn. nieprzechwytywane wyjątki), które pokazują
komunikat błędu bazy danych.

### jak się zabezpieczyć

Aby się zabezpieczyć przed tego typu błędami, jeśli dane wejściowe od użytkownika muszą być użyte w zapytaniu SQL, trzeba tak jak
w przypadku XSS odpowiednio je formatować (ang. escape). Najlepiej skorzystać z przygotowanych zapytań (ang. prepared statements),
polega to na tym, że zanim wykonamy zapytanie przekazujemy do niego kod SQL z zamiennikami (ang. placeholer) np. pytajnikami
i przy wywołaniu zapytania przekazujemy do tak przygotowanego zapytania dane, które zostaną odpowiednio sformatowane przez
bibliotekę bazy danych.

### Co dalej

Korzystając z twojej ulubionej przeglądarki łatwo znajdziesz przykłady zapytań. np. wpisując `"mysql sql injection cheat sheet"`
mysql możesz zastąpić dowolnym innym silnikiem. Istnieją także gotowe aplikacje, którym możecie przetestować "waszą" aplikację
np. bardzo popularna aplikacja wiersza poleceń [sqlmap](http://sqlmap.org/) bardzo fajną jego funkcją, jest dostęp do konsoli sql,
która będzie wykonywała zapytania poprzez SQL injection na atakowanej stronie.

## 3. Session Fixation

## 4. Directory Traversal

## 5. RFI i LFI

RFI (ang. Remote File Inclusion) i LFI (ang. Local File Inclusion) to luka w aplikacji która umożliwia wstrzykiwanie plików do aplikacji.
LFI jest związana także z poprzednim błędem czyli directory traversal jeśli np. aplikacja przyjmuje jako parametr plik który ma zostać wstawiony.

## 6. Wstrzykiwanie CRLF

Polega na dodaniu znaku nowego wiersza i za nim jakiś złośliwych danych. Przykładem może być aplikacja, która wstawia dane
z zapytania np. do nagłówka `Location`. Istnieje jakieś prawdopodobieństwo, w zależności od tego, w jaki sposób dodawany jest ten
nagłówek, że atakujący doda np. znak nowej lini i za nim nowy nagłówek np. `Set-Cookie`, który spowoduje, że będzie można
wykorzystać podatność Session Fixation.

## 7. Cross-Site Request Forgery oraz Click Jacking

Aby zapobiedz atakowi typu click jacking, można wysyłać nagłowek HTTP X-Frame-Options, za pomocą którego możemy ograniczyć
lub uniemożliwić wstawianie naszej aplikacji poprzez iframe-a. Więcej o tym nagłówku możesz przeczytać na
[stronie MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options).

Jeśli myślisz, że jeśli Twoja aplikacja jest bezpieczna przed atakami CSRF, jeśli kożysta z JSON-a i napisana jest w JavaScript,
a nie za pomocą formularzy, to się mylisz. Jeśli nie sprawdzasz jaki jest MIME zapytania po stronie servera, czyli nagłóweka
Content-Type, to istnieje możliwość wysłania zapytania HTTP typu POST z typem text/plain, które może zawierać zwykły tekst.
Więcej informacji na stronie [StackOverlow](https://stackoverflow.com/questions/11008469/are-json-web-services-vulnerable-to-csrf-attacks)
oraz na [pentestmonkey.net](http://pentestmonkey.net/blog/csrf-xml-post-request), która pokazuje jak wysłać XML ale
z JSON-em będzie tak samo.

## 8. Nie walidowane przekierowania

## 9. SSL/TLS i MITM

Jeśli przechowujemy wrażliwe dane w aplikacji, np. gdy mamy system logowania i przechowujemy dane użytkowników,
powinniśmy udostępniać naszą aplikacje poprzez SSL/TLS czyli poprzez protokół https. W przeciwnym wypadku będą możliwe ataki typu
Man-in-the-middle na naszą aplikacje. Atak MITM wygląda tak, że jest osoba, przez którą "przechodzą" zapytania HTTP naszej aplikacji. Haker może np. przechwytywać ruch w publicznej sici wifi ale nie tylko.
Osoba atakująca może np. wstrzykiwać kod JavaScript do naszej aplikacji nawet jeśli nie jest podatna na XSS. W przypadku gdy nasza
aplikacja nie korzysta z HTTPS do przesyłanie hasła na jego odczytanie. **Istnieje także możliwość usunięcia SSL ze źle
skonfigurowanych serwerów www**, więcej informacji możecie znaleźć szukając ["strip ssl MITM"](https://encrypted.google.com/search?hl=pl&q=strip%20ssl%20MITM).
Chociaż przeglądarka Google Chrome w wersji 62 pokazuje komunikat, że strona nie jest bezpieczna w pasku adresu, jeśli strona
zawiera input box-y, a wersja 63 pokazuje je gdy wpisujemy jakiś tekst w dowolny element. Natomiast przeglądarka Firefox od wersji
52 pokazuje komunikat przy input box-ie.

Uwaga: Prawdopodonie w październiku 2017, został wykryty nowy atak na urządzenia z Androidem i Linux-em o nazwie Krack,
który pozwala na złamanie szyfrowania WPA2 poprzez sklonowanie zaszyfrowanej sieci WiFi. **Dzięki niemu możliwy jest atak MITM.**
Więcej na [stronie błędu](https://www.krackattacks.com/), na której można też naleźć krótki filmik, który przedstawia jak wygląda
atak.

Należy jednak pamiętać, że dzięki SSL nie zawsze będziemy zabezpieczenie przed atakami typu MITM, ktoś może np.
[przechwycić połączenie SSL](https://tlseminar.github.io/tls-interception/), wtedy użytkownik otrzyma ostrzeżenie, że połącznie
nie jest bezpieczne (użytkownik może zignorować taki błąd), chyba że atakujący spowoduje zainstalowanie
[certyfikatu urzędu certyfikacji](https://en.wikipedia.org/wiki/Root_certificate) na komputerze ofiary.

Istnieją także ataki na protokół https, możesz o nich przeczytać w
[tym artykule](https://www.acunetix.com/blog/articles/tls-vulnerabilities-attacks-final-part/)

### Co dalej

Możesz obejrzeć prezentacje z konferencji Black Hat (ok godziny) z 2012
["Defeating Ssl Using Sslstrip"](https://www.youtube.com/watch?v=MFol6IMbZ7Y) autorem jest [Moxie Marlinspike](https://moxie.org/).

### Jak się zabezpieczyć

Przede wszystkim najlepiej całą aplikacje ustostępniać poprzez szyfrowane połączenie. Można wymysić też na przeglądarce,
aby zawsze było używane szyfrowanie za pomocą nagłówka HTTP Strict-Transport-Security. Więcej na tym nagłówku na stronie
[MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security). Ważen jest także aby aplikacja nie
pobierała żacnych danych za pomocą HTTP np. obrazków lub plików JS ponieważ wtedy pojawi się komunikat że połączenie nie jest
bezpieczne. Ważne jest także aby użytkownik wiedział, że nie powinien kożystać z aplikacji jeśli przeglądarka "mówi", że
połączenie nie jest bezpieczne.

Warto też sprawdzić czy aplikacja jest podatna na sslstrip, jeśli tak, to można się zabezpieczyć sprawdzając czy request jest
przesłany przez HTTPS. Można by też ustawić ciasteczka jako secure czyli takie, które będą tylko przesyłane przez HTTPS,
ale przypadku ataku MITM atakujący może także, usunąć flagę secure z nagłówka Set-Cookie. Więc sprawdzanie czy zapytanie
jest wysyłane po HTTPS jest jedynym sposobem zabezpieczenia.


## 10. Błędny mechanizm autoryzacji

Występuje np. gdy nasza aplikacja niepoprawnie sprawdza, czy dany zasób powinien być udostępniony danej osobie. oto kilka przykładów:
* Aplikacja może udostępniać panel administracyjny, który jest schowany ale nie zabezpieczony hasłem, albo dostępny dla każdego
  zalogowanego użytkownika, a nie tylko administratora systemu.
* Udostępnianie plików konfiguracyjnych które mogą np. zawierać hasło bazy danych
* Dostęp do plików logów np. w ASP.Net możemy mieć zainstalową aplikacje
  [elmah](https://docs.microsoft.com/en-us/aspnet/web-forms/overview/older-versions-getting-started/deploying-web-site-projects/logging-error-details-with-elmah-cs),
  która loguje wyszytkie błędy w aplikacji razem zapytaniem HTTP, który je wykonał. Może on np. zawierać ciasteczka użytkowników.

## Co dalej

Jako wstęp polecam Darmowy Kurs w języku angielskim (bez logowania) na pluralsight.com
["Hack Yourself First: How to go on the Cyber-Offense"](https://www.pluralsight.com/courses/hack-yourself-first) autorem jest
Troy Hunt ten od [haveibeenpwned.com](https://haveibeenpwned.com), który omawia większość z wymienionych podatności oraz
takie które tutaj nie wymieniłem (na przykład błędy uwierzytelniania). Ma już kilka lat ale nadal zawiera aktualne informacje.
Polecam prędkość 1.4.

