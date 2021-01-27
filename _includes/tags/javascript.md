Na blogu piszę głównie o języku JavaScript

## Ale co to jest JavaScript?

Jest to język programowania, czyli specjalny język który
dzięki silnikowi JavaScript moze być zrozumiany przez komputer.

Język JavaScript jest bardzo ważny, ponieważ prawie każda strona internetowa
używa go w mniejszym lub większym stopniu. Natomiast JavaScript jest nieodzowny
dla aplikacji internetowych, takich jak np. Gmail czy Facebook, które wymagają
języka JavaScript do prawidłowego działania.

## JavaScript jak włączyć?

Jeśli się zastanawiasz jak włączyć JavaScript, to jeśli nic wcześniej nie robiłeś
JavaScript jest już najprawdopodobnie włączony.

Jeśli za jakiegoś powodu JavaScript jest wyłączony patrz następną sekcje.

## Jak wyłączyć obłsugę JavaScript?

Jeśli chcesz tymczasowo wyłączyć JavaScript w przeglądarce, np. aby zobaczyć
jak wygląda strona, gdy JavaScript jest wyłączony. Możesz skożystać z rozszeżenia
dla przeglądarki Google Chrome lub Chromium:

[Toggle JavaScript](https://chrome.google.com/webstore/detail/toggle-javascript/cidlcjdalomndpeagkjpnefhljffbnlo)

W przeglądarce Mozilla Firefox znaduje się podobne rozszerzenie czyli:

[Disable JavaScript](https://addons.mozilla.org/en-US/firefox/addon/disable-javascript/).

Możesz także tego dokonać w ustawieniach np. w przeglądarce Google Chrome.
Wchodzisz w

**Ustawienia > Prywatność i bezpieczeństwo > Ustawienia Witryn > JavaScript**

gdzie możesz wyłączyć JavaScript dla pojedynczej strony, moża wskazać wyjątki gdzie
JavaScript ma być wyłączony lub włączony.

Aby wyłączyć lub włączyć JavaScript w przeglądarce wystarczy wpisać **about:config**
w pasek adresu, klikjąć przycisk że akceptujesz ryzyko. I w polu tekstowym
które się pojawi wpisujesz **JavaScript** pojawią się opcje konfiguracyjne dla
tego zapytania. Aby wyłączyć JavaScript wystarczy zmienić wartość dla **javascript.enabled**
na **false** klikając przucisk z prawej strony.

## Krótka historia Języka JavaScript

Język JavaScript powstał dla przeglądarki Netscape Navigator, która była
bezpośrednim rywalem Internet Explorera w wojnach przeglądarek. Przeglądarka firmy
Netscape dała początek projektowi Open Source Mozilla, dzięki któremu mamy przeglądarkę
Firefox. Udostępnienie kodu źródłowego przeglądarki Netscape było kamieniem milowym
adopcji Open Source (czyli udostępniania kodu źródłowego) przez wiele firm.

Język JavaScript pierwotnie nosił nazwę Mocha i powstał w 1995 roku, zaprojekotwał go
[Brendan Eich](https://pl.wikipedia.org/wiki/Brendan_Eich) w 10 dni. Początkowo jego składnia
miała bazować na języku Scheme, ale managerowie w Netscape nie zgodzili się i dostał
składnie bazującą na jezyku Java, ale wiele cech języka Scheme dostało się do języka,
np. to że funkcje są typem pierwszo klasowym, czyli można je uzywać jak zwykłe wartości,
jak np. liczby czy ciągi znaków.

Mocha była to nazwa kodowa wewnątrz projektu, pierwszą nazwą publiczną był LiveScript,
który potem zmienił się w JavaScript.

## TC39 oraz ECMAScript co to jest?

Dzisiaj jezykiem JavaScript opiekuje się organizacja ECMA, a język jest opisany w standardzie
[ECMAScript](https://pl.wikipedia.org/wiki/ECMAScript). Rozwojem tego języka zajmuje się
[komisja TC39](https://pl.wikipedia.org/wiki/TC39), który co roku od 2015 wypuszcza nową
wersje standardu ECMAScript. Nazwy wersji pierwotnie miały numery kodowe i wersja z 2015,
nosiła nazwę ES6, ale zmieniono sposób nazywania poszczególnych wersji i wersja ES6 nosi nazwę
ES2015, chociaż można się jeszcze spotkać z nazywanie wersji ES6, ES7 itd. Poprawna nazwa
standardu zawiera jednak nazwę roku wydania.

## Jak działa komisja TC39

Komisja skada się z wielu ludzi mi. w jej sklad wchodzią reprezentancji przeglądarek oraz
programiści. Proces dodawania nowych fukcji, podzielony jest na etapy (ang. stages).
Ponumerowane od 0 do 5, gdzie etap 0 to czas gdy można proponować propozycje danej funkcji,
mogą to robić ludzie nie będący członkami komisji TC39. Każdy może takżę komentować propozycje,
dodawane przez innych, najczęściej propozycje są to odpowiednie repozytoria na GitHubie.

Gdy funkcja języka osiąga status/etap 1 oznacza to że jest już sformalizowana, mimo że może się jeszcze zmienić.
Gdy funkcja osiąga status 5 oznacza to że jest już częścią standardu ECMAScript.
