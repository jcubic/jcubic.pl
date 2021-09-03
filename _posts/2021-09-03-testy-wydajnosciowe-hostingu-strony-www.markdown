---
layout: post
title:  "Testy wydajnościowe hostingu i strony WWW: krok po kroku"
date:   2021-09-03 18:24:06+0200
categories:
tags: serwer testowanie wydajność
author: mateuszmazurek
description: W jaki sposób przetestować histing i wybrać ten najbardziej wydajny dla twojej aplikacji internetwoej
image:
 url: "/img/datacenter.jpg"
 alt: "Widoczne ręcę człowieka pracującego przy laptopie obok serwerów"
 attribution: Źródło [pixabay](https://pixabay.com/photos/datacenter-computer-data-firewall-4266405/), [licencja Pixabay](https://pixabay.com/pl/service/license/)
 width: 800
 height: 533
---

Nie chcesz brać za pewnik zapewnień od firmy hostingowej? I bardzo dobrze! Przecież możesz
samodzielnie przeprowadzić test wydajnościowy strony oraz serwera. Zobacz, jak to zrobić,
żeby uzyskać rzetelne wyniki, które pozwolą Ci dokonać dobrego wyboru.

<!-- more -->

Strony internetowej nie możesz umieścić na pierwszym lepszym serwerze - **hosting, który
wybierzesz powinien być dostosowany do Twojej witryny**. Jeżeli spodziewasz się dużej
ilości odwiedzających czy też okresów wzmożonego zainteresowania stroną, blogiem czy
e-sklepem, konieczny jest wybór serwera o dużej wydajności.

Wydajność można zdefiniować jako zdolność do przetworzenia wielu procesów
jednocześnie. Każde wyświetlenie strony składa się z setek czy nawet tysięcy tego typu
procesów. **Na wydajność serwera wpływ mają procesor, pamięć operacyjna, rodzaj dysku
twardego, zainstalowana wersja PHP czy limity przydzielone dla danego klienta.**

Jednak zamiast analizować możliwości każdego takiego elementu z osobna, możesz
przeprowadzić kompleksowy test wydajności hostingu. Da się to zrobić na kilka sposobów.

Autorem tego artykułu gościnnego jest [**Mateusz Mazurek**](https://mateuszmazurek.pl/) z
serwisu testującego i recenzującego hostingi:
[Jak Wybrać Hosting?](https://jakwybrachosting.pl/)

## Strona do testów

Jak ocenić wydajność serwera? Najlepiej sprawdzić, jak zachowa się hosting, gdy strona
będzie odwiedzana przez użytkowników. Wykorzystuje się do tego rozmaite narzędzia - o
których napiszę zaraz. Jednak jeżeli chcesz porównać możliwości hostingów, **strony
umieszczane na serwerach muszą być identyczne**. W tym momencie nie chcesz bowiem
przetestować efektywności samej witryny - a skupić się na parametrach hostingu.

Jaka powinna to być strona? Tutaj nie ma szczegółowych wytycznych, może to być np.:

- **prosta, statyczna witryna w HTML** typu “Hello World” - w takiej sytuacji sama witryna
      ma minimalny wpływ na szybkość realizowanych zapytań podczas testu;
- **blog na WordPressie** - architektura takiej strony, wymaga wykonywania szeregu zapytań
      do baz danych i uruchomienia wielu skryptów, aby witryna mogła zostać wyświetlona. W
      tym wypadku testy wydajności obejmują także wpływ np. zainstalowanego interpretera
      PHP czy limitów na hostingu.

## Test wydajności serwera z Loader.io

**Dzięki narzędziu Loader.io możesz szybko przetestować wydajność hostingu**. Jego obsługa jest bardzo prosta.

1.  Wejdź na stronę [Loader.io](https://loader.io/) i **zarejestruj się**.
2.  **Potwierdź** adres e-mail - kliknij w link weryfikacyjny w otrzymanej wiadomości.
3.  **Wskaż** adres testowanej strony.
4.  **Pobierz** plik weryfikacyjny, a następnie wgraj go na serwer.
5.  Naciśnij przycisk “**New test”** aby rozpocząć testowanie.

W tym momencie przejdziesz do okna ustawień nowego testu.

![interfejs aplikacji loader.io](/img/loader-new-test.png)

W polu Test Type możesz wybierać z trzech rodzajów testów:

- **Clients per test** - pokazuje, jak zachowuje się serwer, gdy konkretna liczba
      użytkowników jest na stronie przez określony czas;
- **Clients per second** - pokazuje, jak zachowuje się serwer, gdy liczba użytkowników
      wzrasta o konkretną wartość co sekundę;
- **Maintain client load** - pokazuje, jak zachowuje się serwer podczas stałego wzrostu
      liczby użytkowników w określonym czasie.

Po wybraniu typu testu, ustaw parametry dotyczące ilości klientów oraz czasu trwania testu.

Ważne są także ustawienia zaawansowane - **Advanced settings**. W tym miejscu możesz
wskazać, w którym momencie należy przerwać test (domyślnie stanie się to, gdy 50% zapytań
będzie realizowanych w czasie przekraczającym 10 sekund).

Z perspektywy wydajności serwera wiele mówi test typu **Maintain Client Load**. Sprawdza
on z jednej strony limity nałożone na klienta hostingu, a z drugiej - parametry sprzętowe
maszyny.

Po uruchomieniu testu, na ekranie pojawi się aktualizowany na bieżąco wykres, który
pokazuje zależność pomiędzy ilością użytkowników na stronie, a czasem potrzebnym na
odpowiedź ze strony serwera. Może to wyglądać np. tak:

![interfejs aplikacji loader.io](/img/loader-test-stop.png)

<small>(Test przerwany z powodu zbyt długiego czasu realizacji zapytań.)</small>

Co widać z tego testu? Na pewno rzucił Ci się w oczy komunikat o błędzie. Faktycznie - **test został przerwany mniej więcej w połowie - w momencie, w którym ponad 50% zapytań było realizowanych przez ponad 10 sekund**.

Po usunięciu takiego warunku, test zostanie przeprowadzony do końca.

![interfejs aplikacji loader.io](/img/loader-test-done.png)

(Test wykonany do końca mimo zbyt długiego czasu realizacji zapytań.)


Niezależnie od tego, czy test został przerwany - czy też nie - otrzymasz rzetelne
informacje dotyczące wydajności hostingu. Jak widzisz - **gdy liczba użytkowników
odwiedzających zaczyna zbliżać się do 400, czas ładowania zaczyna wynosić 10
sekund**. Taki czas jest zdecydowanie za długi - szczególnie zważywszy na to, że
[47% użytkowników zakłada, że strona załaduje się w 2 sekundy](https://blog.exitbee.com/conversion-rate-optimization-tips-how-load-time-affects-conversions-and-what-to-do-about-it/)
lub mniej.

Jakie wnioski możesz wyciągnąć z tego testu? Przede wszystkim:

- jeżeli Twoją stronę będzie odwiedzać wielu użytkowników - wybierz mocniejszy hosting;
- długość ładowania strony wzrasta wraz z ilością użytkowników - w tym wypadku przy 1000
    odwiedzających średni czas to aż 14 sekund;
- jeżeli nie sądzisz, żeby stronę odwiedzało więcej niż 100 użytkowników jednocześnie -
    czas ładowania na poziomie 2,5 sekund jest co najmniej przyzwoity, w związku z tym
    taki hosting może w zupełności wystarczyć.

Loader.io to dobre rozwiązanie, jeżeli chcesz przetestować wydajność hostingu, jednak w wersji bezpłatnej jego możliwości są ograniczone. Gdy nie chcesz inwestować w plan płatny, możesz testować tylko jedną stronę.

## Testy wydajności serwera od Apache

Testy wydajnościowe serwera możesz przeprowadzić także poprzez
[Apache HTTP server benchmarking tool](https://httpd.apache.org/docs/2.4/programs/ab.html). Niestety
takie testy także mają swoje wady, przede wszystkim:

- do wykonania testów, konieczne jest posiadanie odrębnego serwera lub instalacja
    narzędzia na swojej maszynie
- nie da się ich przeprowadzić z hostingu współdzielonego (ze względu na konieczność
    instalacji narzędzia _ab_ w ramach wspomnianego narzędzia).

Aby wykonać test wydajnościowy z użyciem narzędzia od Apache, należy zainstalować je na
serwerze lub komputerze domowym z dobrym łączem. Następnie z wykorzystaniem terminala
przeprowadza się testy strony umieszczonej na innym hostingu. **Narzędzie _ab_ ma szerokie
możliwości dostosowywania parametrów testu** - możesz np. wskazać ilość jednoczesnych
zapytań HTTP, czyli pobrań strony (funkcja -n) oraz jednoczesnych klientów, czyli
użytkowników wysyłających zapytania w tym samym czasie (funkcja -c).

Przykładowy test wprowadzany do terminala może wyglądać następująco:

```
ab -n 200 -c 10 https://nazwastrony.pl/
```

W tej sytuacji testowane jest wyświetlenie strony 200 razy przez 10 użytkowników - czyli
przeprowadzenie 20 następujących po sobie równoległych pobrań witryny przez 10 klientów
jednocześnie.

Po przeprowadzeniu testu otrzymasz informacje o np.:

-   czasie potrzebnym na przeprowadzenie testu;
-   liczbie zapytań zakończonych niepowodzeniem;
-   liczbie zapytań na sekundę;
-   ilości czasu potrzebnego na zrealizowanie zapytania.

Warto wykorzystać te dane do porównania możliwości różnych hostingów. Jeszcze raz w tym
miejscu zaznaczę, że aby uzyskane wyniki nadawały się do porównania - **strony umieszczone
na serwerze muszą być identyczne.**

Oczywiście nie oznacza to, że jeżeli chcesz przeprowadzić test wydajnościowy nie mając do
dyspozycji VPS’a lub serwera dedykowanego, nada się do tego wyłącznie program
Loader.io. Możesz wykorzystać także narzędzia do testowania wydajności stron
internetowych.

## Testy strony WWW

Oczywiście rozumiem pojawiające się obiekcje - **tego typu testy sprawdzają stronę, a nie
serwer.** Jeżeli jednak oprzesz je na najprostszych stronach i porównasz wyniki na różnych
hostingach, to uzyskane wyniki mogą Ci wiele powiedzieć o możliwościach serwera. Żeby to
zrobić:

1.  Zainstaluj **identyczne strony na hostingach, które chcesz przetestować.**
2.  Uruchom narzędzie do testowania stron, np.:
    1.  [PageSpeed Insights](https://developers.google.com/speed/pagespeed/insights/?hl=pl)
    2.  [GTmetrix](https://gtmetrix.com/)
    3.  [WebPageTest](https://www.webpagetest.org/)

4.  Jeżeli narzędzie daje możliwość modyfikacji ustawień - dokonaj testu **z najbliższej
    możliwej lokalizacji**.
5.  Zapoznaj się z wynikami i **porównaj je do rezultatów otrzymanych na innym
    serwerze**. Skup się przede wszystkim na wartościach odnoszących się do szybkości (w
    zależności od narzędzia może to być np. Load time, First Contentful Paint czy First
    Byte). Im mniej - tym lepiej świadczy to o szybkości serwera.

![czas renderowania aplikacji](/img/first-contentful-paint.png)

<small>(First Contentful Paint to jeden z wielu czynników informujących o wydajności serwera.)</small>

Pamiętaj o tym, żeby nie ograniczać się wyłącznie do jednego testu - uśredniony wynik z
testów wykonywanych o różnych porach dnia może dać Ci szerszy obraz. Upewnij się także,
aby podczas porównywania wydajności kilku serwerów, na każdym z nich zainstalowana była
**dokładnie ta sama strona**.

Przeprowadzenie testu wydajnościowego nie musi być trudne. Uzyskane w ten sposób wyniki
wiele mówią na temat faktycznej wydajności serwera oraz postawionej na niej stronie
WWW. **Jeżeli masz możliwość skorzystania z okresu próbnego na hostingu, koniecznie
przeprowadź powyżej przedstawione testy**. Powie Ci to o wiele więcej o możliwościach
serwera niż opis pakietu na stronie dostawcy.
