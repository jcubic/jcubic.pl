---
layout: post
title:  "10 błędów aplikacji www, wykorzystanych przez Hakerów"
date:   2018-01-01 16:30:22+0100
categories:
tags:  hacking www
author: jcubic
description: Najczęstsze błędy aplikacji internetowych, które mogą być wykorzystane przez Hackerów a raczej Crackerów oraz jak się przed nimi bronić.
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
[XSS Filter Evasion Cheat Sheet](https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet) oraz
[XSS (Cross Site Scripting) Prevention Cheat Sheet](https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet),
czyli odpowiednio obchodzenie i zabezpieczanie aplikacji.

## 2. SQL Injection

Ta podatność polega na tym, że jeśli używamy danych od użytkownika w zapytaniu SQL bez odpowiedniego ich formatowania,
złośliwy użytkownik może wstrzyknąć swój kod SQL który wykona się razem z tym naszym.

Na przykład nasza aplikacja może wywoływać takie zapytanie (kod php):

{% highlight php %}
$query = "SELECT * FROM users WHERE username = '$username' and password = '$password'"
{% endhighlight %}

jeśli zmienna `$username` lub `$password` pochodzi od użytkownika, np. z URLa lub zapytania POST, to jest możliwość przekazać
w niej, zamiast hasła, kod SQL np. `' or 1=1;--`. Wynikowe zapytanie po wstawieniu naszego hasła będzie wyglądało następująco:

{% highlight sql %}
SELECT * FROM users WHERE username = 'name' and password = '' or 1=1;--'
{% endhighlight %}

To zapytanie zwróci dane danego użytkownika, mimo ze hasło jest pustym ciągiem znaków. (znaki `;--` oznaczają zakończenie
zapytania oraz zakomentowanie tego co jest za nim, czyli znaku cytatu z oryginalnego zapytania, różne bazy danych mogą mieć
różne znaki komentarza).

Istnieją dwa rodzaje podatności:

### Zwykłe
Wyświetla dane z zapytania na stronie. Za pomocą niego można wykraść wszystkie dane zapisane w bazie danych (np. poprzez zapytanie
UNION SELECT, które pozwala na dodanie, danych z innej tabeli, do zapytania, do którego wstrzykiwany jest kod SQL) ale także hasło
do bazy danych, jeśli aplikacja korzysta z konta root bazy. Jeśli aplikacja umożliwia wykonywanie wielokrotnych zapytań albo gdy
mamy hasło do bazy danych atakujący może dodać do bazy dowolne dane np. jakiś złośliwy kod, gdy aplikacja jest podatna na XSS.

### Ślepe (ang. blind)
Natomiast ślepe może wyświetlać dwa rodzaje stron, np. pustą i z danymi wtedy atakujący może wstrzyknąć zapytanie, które zwraca
prawdę albo fałsz (można do tego celu użyć `CASE..WHEN`) albo prawdę lub zwracać wyjątek np za pomocą `CASE..WHEN` gdzie `ELSE`
wywołuje błąd (można wywołać `convert(int, 'x')` w przypadku bazy MSSQL). Aplikacja może także nie zwracać nic, ale nadal być
podatna na SQL injection, można wtedy np. do zapytania gdy zwraca prawdę dodać opóźnienie i sprawdzać czas jaki zajmuje zapytanie.

W atakach SQL injection może także wykorzystywać błędy zwracane przez aplikacje (tzn. nieprzechwytywane wyjątki), które pokazują
komunikat błędu bazy danych.

### jak się zabezpieczyć

Aby się zabezpieczyć przed tego typu błędami, jeśli dane wejściowe od użytkownika muszą być użyte w zapytaniu SQL, trzeba tak jak
w przypadku XSS odpowiednio je formatować (ang. escape). Najlepiej skorzystać z przygotowanych zapytań (ang. prepared statements),
polega to na tym, że zanim wykonamy zapytanie przekazujemy do niego kod SQL z zamiennikami (ang. placeholer) np. pytajnikami
i przy wywołaniu zapytania przekazujemy do tak przygotowanego zapytania dane, które zostaną odpowiednio sformatowane przez
bibliotekę bazy danych, w zależności od typu (liczbowe wartości będziemy musieli zamienić na liczby, ponieważ zmienne
z zapytania zawsze pobierane są jako ciągi znaków).

### Co dalej

Korzystając z twojej ulubionej przeglądarki łatwo znajdziesz przykłady zapytań. np. wpisując `"mysql sql injection cheat sheet"`
mysql możesz zastąpić dowolnym innym silnikiem. Istnieją także gotowe aplikacje, którym możecie przetestować "waszą" aplikację
np. bardzo popularna aplikacja wiersza poleceń [sqlmap](http://sqlmap.org/) bardzo fajną jego funkcją, jest dostęp do konsoli
sql, która będzie wykonywała zapytania poprzez SQL injection na atakowanej stronie, istnieje też do niego
[GUI](https://github.com/aron-bordin/Tyrant-Sql). Innymi popularnymi narzędziami są
[Havij](https://www.darknet.org.uk/2010/09/havij-advanced-automated-sql-injection-tool/) (działa tylko po Windows) oraz
[sqlninja](http://sqlninja.sourceforge.net/).

Istnieją także ataki na bazy noSQL (czyli nie relacyjne), takie jak mongoDB, gdzie można wstrzyknąć kod javascript
(jest on wewnętrznym językiem do definiowania zapytań oraz funkcji agregujących w mongo) lub obiekty JSON-a bezpośrednio
do bazy. Zobacz ten [artykuł na stonie owasp.org](https://www.owasp.org/index.php/Testing_for_NoSQL_injection) oraz artykuł
[No SQL, No Injection? Examining NoSQL Security](https://arxiv.org/abs/1506.04082) (plik pdf po prawej stronie) oba omawiają
bazę mongoDB, najczęściej używaną bazę typu noSQL.

## 3. SSL/TLS i MITM

Jeśli przechowujemy wrażliwe dane w aplikacji, np. gdy mamy system logowania i przechowujemy dane użytkowników,
powinniśmy udostępniać naszą aplikacje poprzez SSL/TLS czyli poprzez protokół https. W przeciwnym wypadku będą możliwe ataki typu
Man-in-the-middle na naszą aplikacje. Atak MITM wygląda tak, że jest osoba, przez którą "przechodzą" zapytania HTTP naszej aplikacji. Haker może np. przechwytywać ruch w publicznej sici wifi ale nie tylko.
Osoba atakująca może np. wstrzykiwać kod JavaScript do naszej aplikacji nawet jeśli nie jest podatna na XSS. W przypadku gdy nasza
aplikacja nie korzysta z HTTPS do przesyłanie hasła na jego odczytanie. **Istnieje także możliwość usunięcia SSL ze źle
skonfigurowanych serwerów www**, więcej informacji możecie znaleźć szukając ["strip ssl MITM"](https://encrypted.google.com/search?hl=pl&q=strip%20ssl%20MITM).
Chociaż przeglądarka Google Chrome w wersji 62 pokazuje komunikat, że strona nie jest bezpieczna w pasku adresu, jeśli strona
zawiera input box-y, a wersja 63 pokazuje je gdy wpisujemy jakiś tekst w dowolny element. Natomiast przeglądarka
[Firefox od wersji 52 pokazuje ikonkę w pasku adresu oraz komunikat przy input box-ie z hasłem](https://support.mozilla.org/en-US/kb/insecure-password-warning-firefox).

Uwaga: W październiku 2017, został wykryty nowy atak na urządzenia z Androidem i Linux-em o nazwie Krack, który pozwala na
złamanie szyfrowania WPA2 poprzez sklonowanie zaszyfrowanej sieci WiFi. **Dzięki niemu możliwy jest atak MITM.** Więcej na
[stronie błędu](https://www.krackattacks.com/), na której można też znaleźć krótki filmik, który przedstawia jak wygląda
atak.

Należy jednak pamiętać, że dzięki SSL nie zawsze będziemy zabezpieczenie przed atakami typu MITM, ktoś może np.
[przechwycić połączenie SSL](https://tlseminar.github.io/tls-interception/) zmieniając certyfikat, wtedy użytkownik otrzyma ostrzeżenie, że połącznie
nie jest bezpieczne (użytkownik może zignorować taki błąd), chyba że atakujący spowoduje zainstalowanie
[certyfikatu urzędu certyfikacji](https://en.wikipedia.org/wiki/Root_certificate) na komputerze ofiary.

Istnieją także ataki na protokół https, możesz o nich przeczytać w
[tym artykule](https://www.acunetix.com/blog/articles/tls-vulnerabilities-attacks-final-part/)

### Co dalej

Możesz obejrzeć:
* Prezentacje z konferencji Black Hat (ok godziny) z 2012
  ["Defeating Ssl Using Sslstrip"](https://www.youtube.com/watch?v=MFol6IMbZ7Y) autorem jest [Moxie Marlinspike](https://moxie.org/).

### Jak się zabezpieczyć

Przede wszystkim najlepiej całą aplikacje ustostępniać poprzez szyfrowane połączenie (a nie tylko strone logowania/rejestracji)
i dodać przekierowanie ze http na https. Można wymusić też na przeglądarce, aby dla każdego następnego zapytania zawsze
było używane szyfrowanie za pomocą nagłówka HTTP Strict-Transport-Security. Więcej na tym nagłówku na stronie
[MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security). Ważne jest także aby aplikacja
nie pobierała żacnych danych za pomocą HTTP np. obrazków lub plików JS ponieważ wtedy pojawi się komunikat, że połączenie
nie jest bezpieczne lub zostanie zablokowane przez przeglądarkę gdy serwer zwróci wspomniany wcześniej nagłówek. Można też ustawić
ciasteczka jako secure czyli takie, które będą przesyłane tylko poprzez szyfrowane połączenie.


## 4. LFI i RFI

LFI (ang. Local File Inclusion) nazywana jest także atakiem Directory Traversal (lub Path Traversal), ponieważ pozwala na
dołączenie innego, niż zamierzał autor strony/aplikacji. Natomiast RFI jest to skrót od Remote File Inclusion czyli zdalne
wstrzykiwanie pliku.

LFI występuje jeśli np. aplikacja odczytuje plik z katalogu `/templates/` i jako parametr otrzymuje nazwę szablonu
np. `template=main.tmpl` to można np. podać katalog z poza `/templates/` dodając
np. `template=../../../../../../../etc/passwd` w przypadku gdy serwer na którym stoi aplikacja jest to system unix-owy.

Natomiast RFI występuje gdy można wstrzyknąć url pliku gdy aplikacja zakłada że otwiera się plik lokalny.

W obu przypadkach najczęściej wymienia się funkcje, w php, [include](http://php.net/manual/en/function.include.php), która
umożliwia (jeśli nie sprawdzane są dane wejściowe od użytkownika) wstrzykiwanie pliku z kodem php. Jeśli aplikacja nie jest
podatna na RFI ale podatna na LFI, poprzez funkcje `include`, to jeśli jest możliwość wgrywania plików, nawet w przypadku gdy
można dodawać tylko pliki obrazków, to nadal można strzyknąć złośliwy kod php. Pliki obrazków umożliwiają dodawanie
komentarzy w których można dodać kod php który np. może np. wykonywać upload plików już bez ograniczeń.

Do zagnieżdżenia kodu php, wewnątrz komentarza pliku jpeg, można użyć narzędzia `wrjpgcom`, które jest częścią
libjpeg dla systemów Linux. Natomiast do plików png można użyć [png-text-embed](https://github.com/gbenison/png-text-embed).
Istieje także możliwość wstrzyknięcia kodu php za pomocą data URI np. taki ciąg znaków
`data:;base64,PD9waHAgcGhwaW5mbygpOyA/Pgo=`, spowoduje wstrzyknięcie kodu php, który wywoła funkcje `phpinfo`.

Jeśli aplikacja nie korzysta z php warto także sprawdzić czy nie jest podatna.

### Jak się zabezpieczyć

Przede wszystkim trzeba walidować wejście od użytkownika, można usuwać znaki `.` oraz `/`, ale lepszym rozwiązaniem
jest tzw. biała lista (while list) czyli sprawdzanie czy zmienna, która jest pobierana od użytkownika zawiera to co jest
na liście dozwolonych wartości. Można też użyć funkcji `basename`, która zwróci samą nazwę pliku.

## 5. CSRF oraz Click Jacking

CSRF czyli Cross-Site Request Forgery to podatność aplikacji, w której atakujący wywoła akcje, która zrobi coś w systemie,
której nie zainicjował użytkownik. Może to być wysłanie AJAX-em zapytania do serwera (w przypadku ataku
[XSS](#1-cross-site-scripting)) lub wysłanie formularza na zewnętrznej stronie, której link jakoś zostanie wysłany do ofiary.
Taki formularz może wyglądać tak:

{% highlight html %}
<form action="http://podatna-aplikacja.pl/change-password" method="post" target="iframe_name" name="form_name">
  <input type="hidden" name="password" value="hacked"/>
  <input type="hidden" name="confirm" value="hacked"/>
</form>
<script>
document.forms['form_name'].submit();
</script>
<iframe id="iframe" style="display:none"></iframe>
{% endhighlight %}

Jeśli użytkownik otworzy taką stronę wyśle ona zapytanie POST do aplikacji, która zmieni hasło jeśli użytkownik jest zalogowany.
Wynikowa strona nie będzie widoczna dzięki temu że będzie ona w ukrytej ramce. Po wysłaniu formularza strona może gdzieś
przekierować użytkownika. Można to tego użyć zdarzenia `onload` dla ramki.

Jeśli myślisz, że Twoja aplikacja jest bezpieczna przed atakami CSRF, jeśli korzysta z JSON-a i napisana jest w JavaScript, a
nie za pomocą formularzy, to się mylisz. Jeśli nie sprawdzasz jaki jest MIME zapytania po stronie serwera, czyli nagłóweka
Content-Type (powinien być ["application/json"](https://stackoverflow.com/a/477819/387194)), to istnieje możliwość wysłania
zapytania HTTP typu POST z typem text/plain, które może zawierać zwykły tekst. Więcej informacji na stronie
[StackOverlow](https://stackoverflow.com/a/11024387/387194) oraz na
[pentestmonkey.net](http://pentestmonkey.net/blog/csrf-xml-post-request), która pokazuje jak wysłać XML ale z JSON-em będzie
tak samo.

Natomiast Click Jacking polega na tym że atakujący tworzy stronę na której jest ukryty iframe z naszą stroną gdzie wymagane
jest aby ofiara kliknęła na jakiś przycisk np. aby coś wygrać, a w efekcie kliknięty zostanie przycisk z iframe-a. Ukryć
iframe można na dwa sposoby (może jest więcej):

* ustawiając opacity ramki na 0.
* ustawiając z-index ramki pod fikcyjnym przyciskiem i ustawiają dla niego `pointer-events: none`.

### jak się zabezpieczyć

Aby zapobiedz atakowi typu click jacking, można wysyłać nagłowek HTTP X-Frame-Options, za pomocą którego możemy ograniczyć
lub uniemożliwić wstawianie naszej aplikacji poprzez iframe-a. Więcej o tym nagłówku możesz przeczytać na
[stronie MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options).

Najprostszym sposobem, aby uchronić się przed atakiem CSRF, w aplikacji napisanej w JavaScript, jest stworzenie API, które
komunikuje się za pomocą JSON-a, a serwer sprawdza `Content-Type` każdego zapytania. Do komunikacji z API trzeba oczywiście
użyć AJAX-a (a raczej AJAJ-a).

### Co dalej

Jeśli potrzebujesz jakiegoś innego mechanizmu możesz sprawdzić artykuł
["Cross-Site Request Forgery (CSRF) Prevention Cheat Sheet"](https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet).

## 6. Nie sprawdzane przekierowania

Występuje np. gdy aplikacja dodaje to URL parametr redirect z URL-em lub ścieżką na którą ma zostać przekierowany użytkownik.
Może to być np. strona logowania która przekierowuje na stronę, którą użytkownik próbował otworzyć, ale nie był zalogowany.
Jeśli nie jest sprawdzany ten parametr atakujący może podać URL do swojej strony
[phishing-owej](https://pl.wikipedia.org/wiki/Phishing) jako parametr i wysłać takiego link do swojej ofiary.


## 7. Wstrzykiwanie CRLF

Polega na dodaniu znaku nowego wiersza i za nim jakiś złośliwych danych. Przykładem może być aplikacja, która wstawia dane
z zapytania np. do nagłówka `Location` jak w przypadku [nie sprawdzanego przekierowania](#6-nie-sprawdzane-przekierowania).
Istnieje jakieś prawdopodobieństwo, w zależności od tego, w jaki sposób dodawany jest ten nagłówek, że atakujący doda np.
znak nowej lini i za nim nowy nagłówek np. `Set-Cookie`, który spowoduje, że będzie można wykorzystać podatność
[Session Fixation](#8-przechwytywanie-sesji-użytkownika).

## 8. Przechwytywanie sesji użytkownika

Są to dwa sposoby przejęcia sesji użytkownika.

### Session Fixation
W przypadku Session Fixation czyli ustawianiu sesji atakujący wymusza na ofierze użycie swojej sesji może to np. nastąpić gdy
aplikacja jest podatna na wstrzykiwanie nagłówków http (zobacz [Wstrzykiwanie CRLF](#7-wstrzykiwanie-crlf]). Innym sposobem jest
wysłanie linka ofierze z parametrem sesji w URL-u. Php ma opcje pobierania identyfikatora sesji zarówno z URL-a jak i
z ciasteczek.

### Session Hijacking
Jest to metoda odwrotna. Atakujący może przechwycić identyfikator sesji od użytkownika i użyć go u siebie. Mogą być dwa
sposoby przejęcia sesji.
1. [Atack MITM](#3-ssl-tls-i-mitm)
2. [XSS](#1-cross-site-scripting)

### Jak się zabezpieczyć

Jeśli nasza aplikacja ma system logowania, można tworzyć nową sesje przy logowaniu a nie przy pierwszym wchodzeniu na stronę.
Drugą opcją jest zmieniać identyfikator sesji przy każdej akcji (php udostępnia funkcje do tego celu -
[regenerate_session_id()](http://php.net/manual/en/function.session-regenerate-id.php)) lub przynajmniej przy poprawnym
zalogowaniu. W php warto też tworzyć sesje tylko z ciasteczek oraz ustawiać ciasteczka z opcjami secure oraz httpOnly.
W php można użyć takiego kodu:

{% highlight php %}
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
{% endhighlight %}

Te same opcje można włączyć w pliku php.ini.

Warto też całą stronę udostępniać poprze szyfrowane połączenie czyli poprzez protokół HTTPS o czym była mowa w punkcie 3

## 9. Błędny mechanizm autoryzacji

Występuje np. gdy nasza aplikacja niepoprawnie sprawdza, czy dany zasób powinien być udostępniony danej osobie. oto kilka przykładów:
* Aplikacja może udostępniać panel administracyjny, który jest schowany ale nie zabezpieczony hasłem (czyli w tak zwanym
  głębokim ukryciu), albo dostępny dla każdego zalogowanego użytkownika, a nie tylko administratora systemu.
* Udostępnianie plików konfiguracyjnych które mogą np. zawierać hasło bazy danych
* Dostęp do plików logów np. w ASP.Net możemy mieć zainstalowaną bibliotekę
  [elmah](https://docs.microsoft.com/en-us/aspnet/web-forms/overview/older-versions-getting-started/deploying-web-site-projects/logging-error-details-with-elmah-cs),
  która loguje wszystkie błędy w aplikacji razem z zapytaniem HTTP, który je wykonał. Może on np. zawierać ciasteczka
  użytkowników.
* Można się też spotkać z aplikacjami, które w pliku robots.txt umieszczają ścieżki do ukrytych części serwisu. Plik ten
  może być pierwszym miejscem, które sprawdzi atakujący.

Możesz się też spotkać z nazwą Privilige Escalation, czyli zmianą uprawnień. Istnieją dwa jej rodzaje:

* Pionowe - gdy atakujący uzyska dostęp do danych użytkownika o większych uprawnieniach np. administratora.
* Poziome - jeśli atakujący ma dostęp do danych jako inny użytkownik o takich samych prawach dostępu.

Jednym z przykładów zmiany uprawnień może być przypadek, gdy przy zmianie hasła wykonywane jest zapytanie `UPDATE` i jeśli
aplikacja podatna jest na SQL injection. W takim przypadku potrzebne będą dwie podatności typu SQL Injection, jedno `SELECT`
aby sprawdzić jakie są tabele i kolumny i drugie w `UPDATE` aby zmienić uprawnienia. Jeśli biblioteka do bazy danych danej
aplikacji obsługuje wielokrotne zapytania, to nawet ten `UPDATE` nie musi być podatny i wystarczy jakikolwiek `SELECT`.

## 10. Błędy uwierzytelniania

### Ataki na system logowania

Jeśli aplikacja nie sprawdza ilości nieudanych prób logowania (moim zdaniem najlepiej dodać opóźnienie w sekundach, które jest
wielokrotnością liczby 5 i liczbie nieudanych prób wraz z odpowiednim komunikatem), wtedy jest podatna na ataki typu siłowego
(ang. brute force) albo słownikowego. Istnieją gotowe aplikacje np. [Hydra](https://www.thc.org/thc-hydra/), która wykonuje
ataki słownikowe, razem z programem [John the Ripper](http://www.openwall.com/john/), umożliwia ataki brute force.

Jeśli aplikacja wyświetla inny komunikat przy niepoprawnej nazwie użytkownika a inny przy niepoprawnym haśle złamanie
haseł będzie o wiele szybsze ponieważ nie będzie trzeba sprawdzać wszystkich kombinacji użytkownik/hasło.

Innym błędem może być ograniczenie liczby znaków, nie ma żadnego powodu aby uniemożliwiać wpisanie hasła o mniejszej liczbie
znaków niż 100. Nie powinno się też ograniczać liczby znaków jakie można wpisać w hasło np. jeśli sprawdzamy czy hasło zawiera
znaki które mogą próbą SQL Injection lub ataku XSS. Takie znaki powinny być dozwolone aby zwiększyć liczbę potrzebnych
kombinacji przy atakach brute force.

### Przypominanie hasła

Jeśli przy resetowaniu hasła pojawia się komunikat że użytkownik nie istnieje a przy poprawnym, że email został wysłany
może to spowodować, że atakujący może sprawdzać czy dany użytkownik ma konto czy nie. Co spowoduje już wyciek informacji.
Innym błędem może być przypadek, gdy aplikacja resetuje hasło i wysyła go emailem na podany adres, może to spowodować
że atakujący może zablokować komuś dostęp do strony. Wyobraźcie sobie np. portal aukcyjny gdzie jeden użytkownik resetuje
komuś hasło aby wygrać licytacje.

Najlepszym sposobem dodania systemu przypominania hasła jest wyświetlenie komunikatu że email został wysłany. Komunikat
powinien być taki sam w przypadku gdy email jest i nie ma go w bazie. Często można się spotkać z komunikatem że email został
wysłany ja proponuje coś w rodzaju "Jeśli masz konto w naszym portalu/aplikacji to wysłaliśmy ci email z dalszym postępowaniem".
Moim zdaniem taki komunikat jest lepszym z punktu widzenia UX, a nie powoduje żadnych problemów bo jest taki sam w obu
przypadkach.

Najlepiej jeśli email zawiera link z tokenem, który pozwoli zmienić hasło bez logowania, który zostanie unieważniony po np. godzinie od utworzenie lub po poprawnej zmianie hasła.

## Co dalej

Jako wstęp polecam Darmowy Kurs w języku angielskim (bez logowania) na pluralsight.com
["Hack Yourself First: How to go on the Cyber-Offense"](https://www.pluralsight.com/courses/hack-yourself-first) autorem jest
Troy Hunt ten od [haveibeenpwned.com](https://haveibeenpwned.com), który omawia większość z wymienionych podatności oraz
takie które tutaj nie wymieniłem (na przykład błędy uwierzytelniania). Ma już kilka lat ale nadal zawiera aktualne informacje.
Polecam prędkość 1.4.

Innym darmowym szkoleniem jest ["Penetration Testing and Ethical Hacking"](https://www.cybrary.it/course/ethical-hacking/),
którego część jest o aplikacjach internetowych. Ale aby je obejrzeć, trzeba założyć konto. System szkoleń nie jest tak dobrze
zrobiony jak ten na pluralsight.com, korzysta z filmów zagnieżdżonych z vimeo, więc nie ma przyspieszenia, przełączania
na następny film po skończeniu ani autoplay.

Możesz też zainstalować sobie [Kali Linux](https://www.kali.org/) np. na [VirtualBox-ie](https://www.virtualbox.org/)
(lub innej aplikacji do tworzenia maszyn wirtualnych) oraz
[podatną aplikacje OWASP](https://www.owasp.org/index.php/OWASP_Vulnerable_Web_Applications_Directory_Project),
na której można testować lokalnie różne błędy.

w przypadku programu VirtualBox, aby dwie maszyny wirtualne się widziały trzeba ustawić kartę sieciową podatnej maszyny
w tryb mostkowy (ang. bridge mode) lub [utworzyć usługę NAT](https://www.virtualbox.org/manual/ch06.html#network_nat_service),
jeśli nie chcesz aby inni użytkownicy sieci lokalnej nie mieli dostępu do twojej podatnej maszyny.
