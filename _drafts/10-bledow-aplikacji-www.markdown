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

1. Cross-Site Scripting

XSS czyli podatność wstrzykiwania zewnętrznego polega na tym, że hacker/cracker wymusza na naszej aplikacji wykonanie zapytania http do zewnętrznego serwera (wcześniej przez siebie przygotowanego),
które zawiera zastrzeżone dane np. ciasteczka lub hasło. Może to być kod JavaScript ale również [CSS](https://typeofweb.com/2017/12/15/hackowanie-css/) albo XML.

Istnieją jego dwa rodzaje:

## Reflected, czyli odwzorowany
Polega na tym że w zapytaniu np. w parametrze zapytania HTTP typu GET możemy wstawić kod JavaScript, który zostanie odwzorowany w wygenerowanym kodzie.
Nie musi to być także inne zapytanie np. POST wtedy atakujący może utworzyć stronę na innym serwerze, która wyśle formularz do atakowanej stronie. Wtedy url takiej strony musiałby
zostać przesłany ofiarze np. w emailu. Aby taki url wyglądał bezpieczniej atakujący może użyć np. przekierowania które opisane będzie dalej w tym artykule lub skrócić url za pomocą
jednej z wielu usług np. [bitly.com/](https://bitly.com/).

przykładem może być zapytanie `http://example.com/search?term=<script>alert('xss')</script>` jeśli aplikacja wyświetli stronę 404 z wyrażeniem term wstawionym do kodu html strony, będzie to XSS typy odwzorowanego.

## Zapisany (ang. Stored)
Polega on na tym że nasz kod jest zapisywany np. bazie danych i za każdym razem, gdy jakiś użytkownik otworzy stronę wykona się kod, wstrzyknięty przez agresora.

## Co dalej
Polecam dwa artykuły, w języku angielskim, na stronach OWASP (XSS Filter Evasion Cheat Sheet)[https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet] oraz
[XSS (Cross Site Scripting) Prevention Cheat Sheet](https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet), czyli odpowiednio obchodzenie i zabezpieczanie aplikacji.

## Jak się zabezpieczyć
Aby zapobiec atakom tylko XSS, należy filtrować dane od użytkownika oraz bazy danych lub je odpowiednio formatować (ang. escape), jeśli są wstawiane do naszej strony. Możemy np. skorzystać z jakieś frameworka, który zrobi to za nas. Często systemy szablonów nie pozwalają na wstawianie kodu html ze zmiennej i trzeba użyć specjalnej komendy lub filtra by była taka możliwość.

2. SQL Injection

Ta podatność polega na tym, że jeśli używamy danych od użytkownika w zapytaniu SQL bez odpowiedniego ich formatowania, złośliwy użytkownik może wstrzyknąć swój kod SQL który wykona się razem z tym naszym.

Przykład

Nasza aplikacja może wywoływać takie zapytanie (kod php):

{% highlight php %}
$query = "SELECT * FROM users WHERE username = '$username' and password = '$password'"
{% endhighlight %}

jeśli zmienna `$username` lub `$password` pochodzi od użytkownika, np. z URLa lub zapytania POST, to jest możliwość przekazać w niej, zamiast hasła, kod SQL np. `' or 1=1;--`.
Wynikowe zapytanie po wstawieniu naszego hasła będzie wyglądało następująco:

{% highlight sql %}
SELECT * FROM users WHERE username = 'name' and password = '' or 1=1;--'
{% endhighlight %}

To zapytanie zwróci dane danego użytkownika, mimo ze hasło jest pustym ciągiem znaków. (znaki `;--` oznaczają zakończenie zapytania oraz zakomentowanie tego co jest za nim, czyli znaku cytatu z oryginalnego zapytania).

Istnieją dwa rodzaje podatności:

## Zwykłe
Wyświetla dane z zapytania na stronie. Za pomocą niego można wykraść wszystkie dane zapisane w bazie danych (np. poprzez zapytanie UNION SELECT, które pozwala na dodanie, danych z innej tabeli, do zapytania, do którego wstrzykiwany jest kod SQL) ale także hasło do bazy danych, jeśli aplikacja korzysta z konta root bazy. Jeśli aplikacja umożliwia wykonywanie wielokrotnych zapytań albo gdy mamy hasło do bazy danych atakujący może dodać do bazy dowolne dane np. jakiś złośliwy kod, gdy aplikacja jest podatna na XSS.

## Ślepe (ang. blind)
Natomiast ślepe może wyświetlać dwa rodzaje stron, np. pustą i z danymi wtedy atakujący może wstrzyknąć zapytanie, które zwraca prawdę albo fałsz (można do tego celu użyć `CASE..WHEN`) albo prawdę lub zwracać wyjątek np za pomocą `CASE..WHEN` gdzie `ELSE` wywołuje błąd (można wywołać `convert(int, 'x')` w przypadku bazy MSSQL). Aplikacja może także nie zwracać nic, ale nadal być podatna na SQL injection, można wtedy np. do zapytania gdy zwraca prawdę dodać opóźnienie i sprawdzać czas jaki zajmuje zapytanie.

W atakach SQL injection może także wykorzystywać błędy zwracane przez aplikacje (tzn. nieprzechwytywane wyjątki), które pokazują komunikat błędu bazy danych.

## Co dalej

Korzystając z twojej ulubionej przeglądarki łatwo znajdziesz przykłady zapytań. np. wpisując `"mysql sql injection cheat sheet"` mysql możesz zastąpić dowolnym innym silnikiem. Istnieją także
gotowe aplikacje, którym możecie przetestować "waszą" aplikację np. bardzo popularna aplikacja wiersza poleceń [sqlmap](http://sqlmap.org/) bardzo fajną jego funkcją, jest dostęp do konsoli sql, która będzie wykonywała zapytania poprzez SQL injection na atakowanej stronie.

## jak się zabezpieczyć

Aby się zabezpieczyć przed tego typu błędami, jeśli dane wejściowe od użytkownika muszą być użyte w zapytaniu SQL, trzeba tak jak w przypadku XSS odpowiednio je formatować (ang. escape).
Najlepiej skorzystać z przygotowanych zapytań (ang. prepared statements), polega to na tym, że zanim wykonamy zapytanie przekazujemy do niego kod SQL z zamiennikami (ang. placeholer) np. pytajnikami i przy wywołaniu zapytania przekazujemy do tak przygotowanego zapytania dane, które zostaną odpowiednio sformatowane przez bibliotekę bazy danych.

3. Session Fixation

4. Directory Traversal

5. RFI i LFI

RFI (ang. Remote File Inclusion) i LFI (ang. Local File Inclusion) to luka w aplikacji która umożliwia wstrzykiwanie plików do aplikacji.
LFI jest związana także z poprzednim błędem czyli directory traversal jeśli np. aplikacja przyjmuje jako parametr plik który ma zostać wstawiony.

6. Request Tampering

7. Cross-Site Request Forgery

8. Nie sprawdzane przekierowania

9. SSL/TLS i MITM

Jeśli przechowujemy wrażliwe dane w aplikacji, np. gdy mamy system logowania i przechowujemy dane użytkowników,
powinniśmy udostępniać naszą aplikacje poprzez SSL/TLS czyli poprzez protokół https. W przeciwnym wypadku będą możliwe ataki typu Man-in-the-middle na naszą aplikacje.
Atak MITM wygląda tak, że jest osoba, przez którą "przechodzą" zapytania HTTP naszej aplikacji. Haker może np. przechwytywać ruch w publicznej sici wifi ale nie tylko.
Osoba atakująca może np. wstrzykiwać kod JavaScript do naszej aplikacji nawet jeśli nie jest podatna na XSS

10. Błędny mechanizm autoryzacji

Występuje np. gdy nasza aplikacja niepoprawnie sprawdza, czy dany zasób powinien być udostępniony danej osobie. oto kilka przykładów:
* Aplikacja może udostępniać panel administracyjny, który jest schowany ale nie zabezpieczony hasłem, albo dostępny dla każdego
  zalogowanego użytkownika, a nie tylko administratora systemu.
* Udostępnianie plików konfiguracyjnych które mogą np. zawierać hasło bazy danych
* Dostęp do plików logów np. w ASP.Net możemy mieć zainstalową aplikacje [elmah](https://docs.microsoft.com/en-us/aspnet/web-forms/overview/older-versions-getting-started/deploying-web-site-projects/logging-error-details-with-elmah-cs), która loguje wyszytkie błędy w aplikacji razem zapytaniem HTTP, który je wykonał. Może on np. zawierać ciasteczka użytkowników.
