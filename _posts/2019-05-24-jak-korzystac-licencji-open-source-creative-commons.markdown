---
layout: post
title:  "Jak korzystać z licencji Open Source oraz Creative Commons"
date:   2019-05-24 09:37:33+0200
categories:
tags: creative-commons open-source
author: jcubic
description: Wpis o wolnych licencjach takich jak Creative Commons czy MIT oraz GNU GPL. Co można a co nie, jeśli się korzysta z tych licencji.
image:
 url: "/img/open_source.png"
 alt: "Grafika z ikonami przedstawiającymi wolne licencje"
 width: 800
 height: 520
---



W tym wpisie przedstawię jak działa Creative Commons. Co można zrobić z programem, jeśli ma licencję MIT oraz
GNU GPL. Opowiem także jak używać treści (głównie zdjęć), którymi można się dzielić, dzięki licencji Creative
Commons. Jak podpisać zdjęcie, które ma licencje Creative Commons, gdy użyje się je na swojej stronie.

Jeśli korzystasz z programów Open Source (czyli Wolnego i Otwartego Oprogramowania) lub gdy piszesz bloga lub
tworzyć cokolwiek w internecie to musisz przeczytać ten wpis, szczególnie jeśli jeszcze nie wiesz co to jest
licencja MIT, GPL czy Creative Commons.  Jeśli piszesz aplikacje w JavaScript z użyciem pakietów z
[NPM](https://www.npmjs.com/) oraz korzystasz z narzędzi takich jak [Webpack](https://webpack.js.org/) czy
[Rollup](https://rollupjs.org/guide/en/) musisz wiedzieć czego nie robić.

Jeśli znajdujesz jakiś zdjęcia w internecie i dzielisz się nimi z innymi, także powinieneś przeczytać ten
wpis. Przynajmniej drugą jego część dotyczącą Creative Commons.

<!-- more -->

## Kod źródłowy oraz Wolne i Otwarte Oprogramowanie

Jak każda twórcza działalność, tak samo kod źródłowy posiada autora (lub wielu autorów). Jeśli autorzy
zdecydują się udostępnić swój kod światu, jako wolne oprogramowanie, muszą użyć jakiejś licencji.  Licencja
jest to akt prawny który z założenia miał nadawać dodatkowe restrykcje na dane dzieło. Jednak dzięki takiemu
osobnikowi jak [Richard Stallman](https://pl.wikipedia.org/wiki/Richard_Stallman), który jako pierwszy
stworzył licencję, która umożliwia kopiowanie dzieła bez ograniczeń, mamy Wolne Oprogramowanie, nazywane także
Otwartym (ang Free and Open Source Software).  Mechanizm ten, który został nazwany Copyleft, jest to odwrócenie
działania mechanizmu Copyright (w Polsce nazywa się to Prawo autorskie, co nie jest tak dwuznaczne jak w
języku Angielskim). W zależności od licencji autor nadaje odpowiednie prawo dotyczące kopiowanie oraz
modyfikowania kodu.

![Conference by Richard Stallman "Free Software: Human Rights in Your Computer", 2014](/img/Richard_Stallman.jpg)
*Autor [Thesupermat](https://commons.wikimedia.org/wiki/User:Thesupermat); Źródło [Wikipedia](https://commons.wikimedia.org/wiki/File:Richard_Stallman_-_F%C3%AAte_de_l%27Humanit%C3%A9_2014_-_010_-_small.jpg); Licencja [CC-BY-SA-3.0](https://creativecommons.org/licenses/by-sa/3.0/deed.en)*

## [Licencja MIT](https://choosealicense.com/licenses/mit/)

Jeśli chodzi o kod źródłowy bibliotek Open Source w JavaScript jest to najczęściej stosowana licencja. Mówi
ona, że każdy może zmodyfikować lub kopiować do woli tylko musi dodać notkę o prawie autorskim oraz
licencji. Można używać biblioteki w celach komercyjnych. Można też taką bibliotekę zmodyfikować i nadać jej
inną licencje. Można także użyć jej w większej aplikacji.  Nie wymaga abyśmy udostępniali kod źródłowy
wynikowej aplikacji, która używa biblioteki lub ją modyfikuje.

## [Licencja GNU GPL (czyli GNU General Public License)](https://choosealicense.com/licensegpl-3.0/)

Bardzo popularna licencja napisana przez wspomnianego wcześniej Richarda Stallmana, doczekała się wersji 3,
która współgra z prawem patentowym (w USA można opatentować oprogramowanie w odróżnieniu od Polski).  Wersja
3.0 była kontrowersyjna i np. niektóre projekty zmieniły zapis, aby używać tylko wersji 2.0 (ponieważ domyślny
zis mówi, że można używać licencji ten lub dowolnej kolejnej). Takimi projektami były np. jądro Linux.
Licencja zezwala na dowolne kopiowanie, ale każda kopia musi mieć informacje o licencji oraz autorze. Można do
woli modyfikować kod źródłowy, ale zmodyfikowana wersja musi mieć tą samą licencje i musi być zapewniony
dostęp do kodu źródłowego w zmodyfikowanej wersji.

## [Licencja GNU AGPL (Affero General Public License)](https://choosealicense.com/licenses/agpl-3.0/)

Rzadko używana jest to wersja licencji GPL, ale z klauzulą, która mówi, że udostępnianie pliku źródłowego na
serwerze (nawet w wersji skompilowanej) jest już traktowane jako kopiowanie. Czyli jeśli coś ma taką licencje
to trzeba udostępnić kod źródłowy wszystkich modyfikacji nawet jeśli używasz ja tylko dla siebie na swojej
stronie domowej. Tak działa to w przypadku
[komentarzy na tym blogu](/2018/12/system-komentarzy-hashover-alternatywa-disqus.html), na szczęście autor wpadł
na fajny pomysł i można obejrzeć kod źródłowy klikając link pod komentarzami (chyba dodałem kilka zmian, aby
działał na mojej stronie).

## [Licencja GNU LGPL (Lesser General Public License)](https://choosealicense.com/licenses/pl-3.0/))

Lekka wersja GPL, której skrót przetłumaczyć można jako pomniejszy. Skrót początkowo był rozwijany jako
Library. I przeznaczony był głównie do kodu źródłowego bibliotek, aby można było linkować statycznie biblioteki
z programem napisanym w języku C. Wynikowy program który zawiera w sobie podlinkowana bibliotekę nie musi
udostępniać swojego kodu źródłowego.

## [Licencja Apacche 2.0](https://choosealicense.com/licenses/apache-2.0/)

Licencja podobna do MIT, z wyjątkiem tego, że udziela praw do patentów oraz wymaga, aby modyfikacje były udokumentowane.

## [Webpack](https://webpack.js.org/) oraz [NPM](https://www.npmjs.com/)

Technicznie rzecz biorąc nie jest łamaniem licencji jeśli ktoś używa dowolnej biblioteki JavaScript z
npm. Chyba że jest to licencja AGPL, co chyba jest rzadkością i byłoby problematyczne ze względu na to, że
wszystko, nawet to co linkuje do projektu z tą licencja, musi być na tej licencji (czyli kod źródłowy samej
aplikacji). Nawet nie chce myśleć co by było gdyby jakiś projekt na tej licencji był w npm i był używany.
Jeśli znasz jakiś taki projekt napisz w komentarzu.

## Usuwanie notki z licencją z kodu, który wypluwa Webpack

Jeśli korzystasz z narzędzia takiego jak Webpack czy Rollup to zawsze powinieneś zostawić notkę o autorze,
licencji oraz linku do projektu. Usuwanie notki o autorze z pliku JS lub CSS jest łamaniem licencji. Większość
z nich wymaga, aby każda kopia miała notkę o autorze, czyli najczęściej pierwszy specjalny komentarz w
kodzie. Można się kłócić czy udostępnianie na serwerze jest kopiowaniem.  Według GNU GPL nie, dlatego powstała
licencja GNU AGPL. Ale na pewno nie jest etyczne usuwanie info o autorze, to nie jest fajne, gdy ktoś używa
czegoś co w trudzie stworzyłeś i nie przypisze Ciebie jako autora.  I nie chodzi o to, że jest strona gdzie są
linki to użytych projektów (fajnie jak coś takiego jest), ale chodzi mi to, że nie ma w kodzie źródłowym
żadnej informacji skąd kod pochodzi.

Kiedyś znalazłem taką stronę, która korzystała z mojej biblioteki (szukałam kto używa starej wersji, żeby
powiadomić, że nie działa w nowej wersji Chrome) i nie było info o wersji ani autora na jednej z takich
stron. Co nie było fajne. Tak się nie powinno robić. Jeśli tak robisz to przestań.

Jedyny wyjątek, ale tylko według mnie (a nie licencji) to np. gdy ktoś pisze aplikację
tzw. [ARG](https://en.wikipedia.org/wiki/Alternate_reality_game) (ang. Alternate reality game), gdzie trzeba
ukryć co się da (mój projekt użyty był w jednej takiej grze i nie miał notki copyright) co moim zdaniem było
fajne szczególnie, że i tak osoby, które grały w tą grę znaleźli skąd ten kod pochodził (bo to nie jest w
sumie takie trudne) i ktoś na Twitterze nawet pytał mnie o to czy coś wiem na temat gry. Mój użyty projekt
wtedy był jeszcze mało znany, teraz pewnie już nie pomyśleliby, że to wskazówka.

## Dzieła Artystyczne

Open Source to głównie programy komputerowe ale dzięki osobom takim jak
[Lawrence Lessig](https://pl.wikipedia.org/wiki/Lawrence_Lessig) czy
[Aaron Swartz](https://pl.wikipedia.org/wiki/Aaron_Swartz) (polecam film dokumentalny o nim
["The Internet's Own Boy"](https://en.wikipedia.org/wiki/The_Internet%27s_Own_Boy)) mamy możliwość korzystania
z tych samych dobrodziejstw co kod źródłowy programów komputerowych. Czyli mamy możliwość nadania pewnych praw
do wolności dzielenia się dowolnym dziełem, które stworzymy (mogą to być np. autorska muzyka, filmy, czy
książki, ale także wpisy na blogu czy inny tekst)

![Lawrence Lessig oraz Aaron Swartz w koszulce Creative Commons](https://jcubic.pl/img/Aaron_Swartz_and_Lawrence_Lessig.jpg)
*Autor [Gohsuke Takama](https://www.flickr.com/photos/gohsuket/); Źródło [Wikipedia](https://commons.wikimedia.org/wiki/File:Aaron_Swartz_and_Lawrence_Lessig.jpg); Licencja [CC-BY](https://creativecommons.org/licenses/by/2.0/deed.en)*


## Co to jest Creative Commons?

Jest to zestaw licencji dających pewne prawa do kopiowania (po angielsku nazywa się to Copyright License),
które daje ograniczone lub nieograniczone prawa innym do danego dzieła. Stosowana głównie do dzieł
artystycznych czyli książek, muzyki, filmów, grafiki i zdjęć. rzadziej do kodu źródłowego programów
komputerowych.

Mimo to warto znać tą licencje i wiedzieć jak ona działa. Licencje Creative Commons składa się z kloców które
dają pewne ograniczone prawa. Np. nie pozwala na to, aby ktoś do filmu o głodujących dzieciach w Afryce, dodał
wstawkę o Hitlerze, ale pozwala na dowolne oglądanie oraz kopiowanie. Tak żeby jak najwięcej osób dany film
obejrzało, co może być głównym celem nakręcenia takie filmu.

## Licencja CC składa się z taki klocków:

* Attribution (uznanie Autorstwa) mówi że musisz podać imie i nazwisko autora jeśli kopiujesz dane dzieło.
* Share-Alike (Na tych samych warunkach) mówi że jeśli zmodyfikujesz dzieło które ma tą licencje musisz swoją
  kopie udostępnić na tej samej licencji (czyli tak samo jak GNU GPL do kodu źródłowego która była kiedyś
  nazywana licencją wirusową, prawdopodobnie głównie przez Microsoft, który teraz jest pro Open Source).
* Non-Commercial (Użycie niekomercyjne) mówi że jeśli coś ma ten klocek, to nie możesz użyć tego dzieła, aby
  czerpać z tego korzyści finansowych.
* Non-Derivative (Bez utworów zależnych) mówi że nie można modyfikować dzieła (czyli tak jak przykład z filmem
  o dzieciach w Afryce).

Te klocki w dowolnej kombinacji dają zestaw licencji Creative Commons oprócz nich jest jeszcze:

* Creative Commons Zero - mówi ona że dzieło jest w domenie publicznej - a dokładnie każdy ma takie prawa
  jakby było w Domenie Publicznej. Czyli może dowolnie kopiować i modyfikować. Przy czym nie ma ona klocka
  Attribution, czyli nie trzeba przypisywać autora do danego dzieła jeśli je kopiujemy.

Ale jest coś jeszcze. Ze względu na to, że jest to licencja i autor ma prawo do kopiowania (posiada prawo
autorskie) nie można takiego dzieła wziąć i udostępniać innym w innej licencji. Tak naprawdę to nawet jeśli dzieło
trafi do domeny publicznej (ponieważ minie odpowiednia liczba lat), także nie można nadać dziełu jakiejś licencji
(zobacz artykuł [Getty Images pozwane za pobieranie opłaty za zdjęcia z domeny publicznej](https://fotoblogia.pl/13711,getty-images-pozwane-za-pobieranie-oplaty-za-zdjecia-z-domeny-publicznej)).

## Ale czy jak dodam licencje Creative Commons to nie stracę prawa autorskiego?

I tutaj muszę dodać coś co może kogoś zdziwić, nawet jeśli dzieło ma licencje CC-0 czyli jest tak jakby było w
domenie publicznej, to nie znaczy że ktoś może przypisać sobie autorstwo danego dzieła i podpisać je swoim
nazwiskiem. Jest to wbrew prawu autorskiemu, które mówi że nie można zbyć się praw autorskich osobistych,
czyli jeśli coś stworzysz to już zawsze będziesz tego autorem. Czyli jeśli ktoś znajdzie zdjęcie w sieci które
ma licencje CC-0 (czyli Domena Publiczna - dowolne użycie, nawet komercyjne) to nie znaczy, że może np. wziąć
to zdjęcie podpisać je, że jest jego autorem i sprzedawać jako swoje. Nie może też zmienić licencji na inną
bardziej restrykcyjną chyba że w dużym stopniu zmodyfikuje dane dzieło. Np. jak ktoś użyje do utworu Hip-Hop
kawałka na licencji Creative-Commons które nie ma Non-Commercial (Użycie niekomercyjne) oraz Non-Derivative
(Bez utworów zależnych) to może zmiksować taki utwór (użyć jako sample) i sprzedawać taki utwór i nie musi nic
płacić ani podpisywać żadnej licencji.

## Używanie zdjęć w internecie

I tutaj muszę napisać o udostępnianiu zdjęć znalezionych w internecie na swoje stronie oraz w mediach
społecznościowych np. Facebook czy Twitter.

Jeśli znajdziesz jakieś zdjęcie które nie ma licencji Creative Commons i ma Copyright (C) to znaczy, że nie
możesz go nigdzie udostępnić. Według prawa autorskiego wyłączne prawo ma autor zdjęcia. To że coś jest w
internecie to nie znaczy, że można to wykorzystać i jest darmowe.

## Nieświadome łamanie prawa autorskiego licencji Creative Commons

Wiele ludzi nie zdaje sobie sprawy, że nie można tak po prostu używać czyich zdjęć. Nawet jeśli są na licencji
Creative Commons (wielu ludzi nawet nie wie co to jest). Oprócz praw osoba, która używa dzieła ma
też obowiązki. Czyli musi podpisać dzieło, nie sprzedawać zdjęć lub czegoś co ze zdjęcia lub innego dzieła
korzysta, lub nie modyfikować gdy tego wymaga licencja.

Wiem to niestety z własnego doświadczenia, do wielu lat hobbistycznie zajmuje się fotografią i udostępniam
większość swoich zdjęć na licencji Creative Commons na stronach
[Wikimedia Commons](https://commons.wikimedia.org/wiki/User:Jcubic) oraz
[flickr](https://www.flickr.com/photos/jcubic/). I jeśli chodzi o zdjęcia na flickerze, to te starsze, które są
już od jakiegoś czasu w wyszukiwarce Google, w większości jest są podpisane moim nazwiskiem, co jest wbrew
prawu autorskiemu oraz licencji Creative Commons.

A znaleźć można bardzo łatwo. Wystarczy wkleić link do zdjęcia w wyszukiwaniu obrazem. Google korzysta ze
sztucznej inteligencji, aby wyszukać podobne zdjęcia (tak przynajmniej mi się wydaje). Można wyszukać
wszystkie kopie danego zdjęcia w różnych formatach. Można nawet znaleźć oryginał zeskanowanego zdjęcia, które
nawet nie musi być w całości i zazwyczaj można poznać, kto jest autorem.

## Jak podpisywać zdjęcia (i nie tylko) na licencji Creative Commons

Zanim powstał projekt Creative Commons zazwyczaj dodawało się taką notkę.:

Copyright (C) 2007-2008 Jan Kowalski all right reserved

Jeśli chodzi o kod źródłowy to nadal tak to wygląda. Tylko jak więcej osób pracuje, nad jakimś projektem Open
Source, to zazwyczaj jest więcej praw autorskich i różne lata i jakaś wersja wolnej licencji (Open Source)
np.:

Copyright (C) 2007-2008 Jan Kowalski
Copyright (C) 2009-2019 Piotr Nowak
Released under MIT license

Licencje Creative Commons zastąpiły "all right reserved" (czyli wszelkie prawa zastrzeżone) sloganem "some
right reserved" (czyli niektóre prawa zastrzeżone), ale jest to raczej slogan marketingowy, którego się nie
stosuje.

Jeśli chodzi o Creative Commons to podpis powinien wyglądać tak.:

> Autor Jan Kowalski; źródło [Wikipedia](https://pl.wikipedia.org/wiki/Jan_Kowalski);
> licencja [CC-SA](https://creativecommons.org/licenses/by-sa/4.0/deed.pl)

Jeśli autor ma swoją stronę to może to być jego strona domowa (link pod nazwiskiem) albo profil na stronie
skąd się wzięło zdjęcie np. flickr. Po takim linku może też ktoś znaleźć stronę oraz oryginał zdjęcia lub
innego utworu. Jeśli autor ma stronę domową i link nie ma `rel="nofollow"` to może to być też forma
podziękowania, które polepszy pozycje w wyszukiwarkach (czyli polepszy SEO strony autora).

## A co z tym blogiem?

Tak jeśli pierwszy raz czytasz tego bloga lub jeśli nie zwróciłeś uwagi, to jeśli przejedziesz na dół strony
to zobaczysz, że treść jest na licencji Creative Commons, Uznanie Autorstwa, Na tych samych warunkach (czyli
skrócie CC-BA-SA) czyli:

* możesz skopiować treść tego wpisu
* udostępnić ją na swoim blogu lub inny miejscu
* możesz go wydrukować i sprzedawać wydrukowaną kopię
* możesz go zmodyfikować i udostępnić zmodyfikowana wersje

Ale musisz też:

* do każdej kopii podać moje imię i nazwisko czyli Jakub T. Jankiewicz
* miejsce skąd wziąłeś artykuł czyli blog Głównie JavaScript
* link do artykułu i najlepiej link do bloga
* link do licencji (z chwilą pisania tego wpisu była to wersja angielska, ale międzynarodowa
  [https://creativecommons.org/licenses/by-sa/4.0/](https://creativecommons.org/licenses/by-sa/4.0/)).

I ile jest to możliwe musi być też w wersji drukowanej (nie koniecznie linki ale np. autor, nazwa bloga oraz
licencja już powinny się pojawić).

## Ale co jeśli coś nie ma licencji Creative Commons

Jeśli nie ma żadnej licencji dodanego do dzieła, to znaczy że nie możesz go po prostu kopiować, znaczy to że
dane zdjęcie ma pełne prawa autorskie, czyli all right reserved.  Oznacza to brak możliwości jakiegokolwiek
kopiowania (chyba że autor udzieli zgodę). Jeśli znajdziesz jakieś zdjęcie na czyimś blogu to warto użyć
wyszukiwania obrazem w Google, aby znaleźć oryginał, jeśli nie jest podpisany. Aby wyszukiwać obrazem
wystarczy wejść na stronę [Google Images](https://www.google.com/search?tbm=isch&q=vector%20svg&tbs=imgo:1) i
kliknąć ikonkę aparatu fotograficznego przy polu wyszukania. Można wtedy podać link do zdjęcia (można kliknąć
prawym klawiszem myszy na zdjęciu i wybrać kopiuj link lub grafikę). Można także złapać obrazek w wyszukiwarce
i upuścić na pole które się pojawi pod polem wyszukiwania (można także upuścić zdjęcie z własnego komputera).
