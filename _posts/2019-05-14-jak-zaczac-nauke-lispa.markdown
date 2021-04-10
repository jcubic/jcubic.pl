---
layout: post
title:  "Jak zacząć uczyć się języka LISP"
date:   2019-05-14 01:05:54+0200
categories:
tags: lisp języki
author: jcubic
description: Wpis o tym jak zacząć naukę programowania w języku LISP. Jaki dialekt wybrać Scheme, Clojure czy Common LISP. Czy są jakieś kursy wideo i czy są książki o języku programowania LISP.
image:
 url: "/img/lisp-alien.png"
 alt: "Grafika z obcym z pięcioma oczami, flagą LISP oraz napisem Made with secret alien technology"
 width: 800
 height: 500
 attribution: Jakub T. Jankiewicz, licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/4.0/). źródło na [GitHub-ie](https://github.com/jcubic/jcubic.pl/blob/master/img/lisp-alien.svg) bazuje na [Lisperati](http://www.lisperati.com/logo.html) licencja Public Domain.
related:
  -
    name: "Parser S-Wyrażeń (języka LISP) w JavaScript"
    url: "/2019/06/parser-jezyka-lisp-javascript.html"
sitemap:
  lastmod: 2021-02-12 14:03:00+0100
---


Dzisiaj będzie trochę inny wpis. Nie będzie dotyczył programowania stron internetowych, czyli głównego tematu
bloga. Postanowiłem napisać krótki wpis opisujący, jak zacząć naukę programowania w języku LISP. Jest to język,
który powinien poznać każdy programista.

<!-- more -->

## Wprowadzenie

LISP to jeden z dwóch najstarszych języków programowania, który nadal jest używany. Drugi to Fortran.
Powstał on w latach 50 na MIT. Zaprojektował go [John McCarthy](https://pl.wikipedia.org/wiki/John_McCarthy).
Ciekawostką może być to, że miał być tylko teoretyczną (matematyczną) alternatywą dla
[maszyny Turinag-a](https://pl.wikipedia.org/wiki/Maszyna_Turinga). Ale jeden ze studentów McCarthy’ego
([Steve Russell](https://pl.wikipedia.org/wiki/Steve_Russell)) postanowił napisać główny element języka, czyli
funkcje `eval`. Pierwotna wersja (ta zaprojektowana przez McCarthy’ego) nie miała takiej składni
(czyli S-Wyrażeń), ale pisząc kod Russell nie mógł użyć takich samych znaków na klawiaturze, więc użył
okrągłych nawiasów. Język oparty jest na [rachunku lambda](https://pl.wikipedia.org/wiki/Rachunek_lambda)
stworzonym przez [Alonzo Churcha](https://pl.wikipedia.org/wiki/Alonzo_Church), jest to matematyczny zapis,
który powstał mniej więcej w tym samym czasie co Maszyna Turinga, jako uniwersalny język opisujący
dowolne obliczenia.

Jako zachętę zacytuje Erica Raymonda:

> "LISP is worth learning for a different reason — the profound enlightenment experience you will
> have when you finally get it. That experience will make you a better programmer for the rest of
> your days, even if you never actually use LISP itself a lot."
>
> ["How to Become a Hacker"](http://www.catb.org/~esr/faqs/hacker-howto.html)
>
> **Tłumaczenie**: "LISP jest wart nauki dla głębokiego uczucia oświecenia, kiedy go wreszcie
> zrozumiesz. To doświadczenie uczyni z ciebie o wiele lepszego programistę, nawet jeśli nie używasz
> LISPa zbyt często."
>
> ["Jak zostać Hakerem"](http://www.mkgajwer.jgora.net/ers.html)


## Krótko o kodzie LISP-a

Kod LISP-a skład się z [S-Wyrażeń](https://pl.wikipedia.org/wiki/S-wyra%C5%BCenie) (listy w
nawiasach gdzie elementy są oddzielone spacjami) i ma notacje prefix-ową to znaczy, że nazwa funkcji
jest pierwszym elementem listy, np.:

{% highlight scheme %}
(+ (- 10 2) 100)
{% endhighlight %}

Jest to wyrażenie, które oblicza `(10 - 2) + 100`. Co ciekawe język nie ma operatorów tylko funkcje,
które są pierwszym elementem listy. `+` nie jest to operator dodawania, ale funkcja, która jest
przypisana do symbolu plus. Pierwszy element to nie musi być także nazwa. Może to być wyrażenie,
które w wyniku zwróci nową funkcje czyli. tzw funkcja wyższego rzędu.

Przykład funkcji obliczającej silnię w języku programowania Scheme:

{% highlight scheme %}
(define (! n)
   (if (zero? n)
       1
       (* n (! (- n 1)))))
{% endhighlight %}

Może wyglądać dziwnie i odstraszać ale po chwili nauki języka, można się przyzwyczaić, a jak pozna się
makra, to już taki zapis jest oczywisty i zaczyna go brakować w innych językach.

Tak samo jak są definiowane funkcje wbudowane jak `+`, tak samo można definiować własne funkcje, jak
np. wykrzyknik. W niektórych dialektach nie ma różnicy między wbudowaną funkcją, a tą z biblioteki
standardowej. Można np, napisać sobie funkcje `+` która odejmuje liczby (nie żebym zachęcał, ale
można). Ale już użyteczniejsze może być użycie funkcji `+`, która operuje na innych obiektach
jak np. [macierzach](https://pl.wikipedia.org/wiki/Macierz) w programie do grafiki 3D.

## Jaki dialekt wybrać

Do wyboru są 3 główne dialekty:

### Common LISP

Przez programistów LISP-a jest polecany jako najpotężniejszy dialekt. Ale nie polecam go na początek, ponieważ
można się zagubić w masie funkcji z biblioteki standardowej, które są w nim dostępne, a traci się możliwość
poznania istoty LISP-a.

### Scheme

Jest bardzo prosty dialekt razem z Common LISP-em dwa najczęściej używane. Powstał w latach 70 dzięki takim
ludziom jak
[Guy L. Steele](https://en.wikipedia.org/wiki/Guy_L._Steele_Jr.) i
[Gerald Jay Sussman](https://en.wikipedia.org/wiki/Gerald_Jay_Sussman). Jego zaletą jest prostota.
Mała biblioteka standardowa, więc jeśli chce się mieć podstawowe funkcje to trzeba je najpierw napisać.
Jest to świetny sposób, aby poznać język - pisanie prostych funkcji do różnych celów które mogą się przydać
w przyszłości.

### Clojure

Jest to nowoczesny dialekt LISP-a, dzięki któremu LISP stał się bardziej popularny.  W Polsce można
nawet czasami znaleźć pracę, gdzie wymagają tego języka. Działa na maszynie wirtualnej JVM (czyli to
na czym uruchamiana jest Java). Zaprojektowany przez
[Richa Hickeya](https://pl.wikipedia.org/wiki/Rich_Hickey). Więcej o języku na
[Wikipedii](https://pl.wikipedia.org/wiki/Clojure).

## Jak zacząć?

Naukę programowania w języku LISP zacząłbym od dialektu Scheme, który jest prosty w nauce.
Polecam najpierw obejrzenie wykładów SICP ok 20 godzin oglądania (10 dwu częściowych lekcji)
prowadzonego przez Abelsona i Sussmana (ten drugi zaprojektował język Scheme) prowadzone na MIT dla
pracowników HP w latach 80 ([link MIT](http://groups.csail.mit.edu/mac/classes/6.001/abelson-sussman-lectures/)
oraz [link YouTube](https://www.youtube.com/playlist?list=PL8FE88AA54363BC46)). Wykładowcy razem napisali książkę
[Struktura i Implementacja Programów Komputerowych](http://lubimyczytac.pl/ksiazka/135282/struktura-i-interpretacja-programow-komputerowych), która była obowiązkowa na studiach na MIT (nie wiem czy nadal jest). Można też znaleźć w
[sieci wykłady z uniwersytetu w Berkley z roku 2010](https://www.youtube.com/watch?v=4leZ1Ca4f0g&list=PLhMnuBfGeCDNgVzLPxF9o5UNKG1b-LFY9). Osobiście oglądałem tylko te Sussmana i Abelsona.

Jeśli studiowałeś informatykę na uczelni wyższej, to nie powinno być dla ciebie problemem obejrzenie wykładów
(chyba, że jesteś z tych co olewali studia).  Jeśli nie studiowałeś i wytrzymasz wykłady i zaczynasz naukę
programowania to możesz też znaleźć masę dobrych wykładów w sieci z programowania i ogólnie informatyki z
różnych uczelni wyższych z USA.

Jeśli chcesz wypróbować kilka wyrażeń, możesz zobaczyć mój
[interpreter języka lisp w JavaScript](https://lips.js.org), o nazwie LIPS (nazwa jest to
rekurencyjny skrót: LIPS Is Pretty Simple), aktualnie pracuje nad wersją 1.0, która ma być 100%
zgodna z językiem Scheme (specyfikacjami R5RS i R7RS).  Na razie główne różnice to brak kontynuacji
oraz obsługi rekurencji ogonowej (nie wiem czy wejdą do wersji 1.0). Więc nie można napisać prostej
rekurencyjnej funkcji obliczającej silnię (jest to najprostszy przykład rekurencji) i obliczyć
np. silnię z 10 000.  Ale obsługuje liczby typu BigInt, dzięki czemu można obliczyć silnię ze 100 za
pomocą prostej funkcji.  Fajnie się integruje z językiem JavaScript i obsługuje automatycznie kod
asynchroniczny, tzn. jak masz
[obietnicę](/2018/05/asynchronicznosc-javascript-obietnice.html), to jest ona automatycznie
odwijana, jakby jej nie było. Tak jak w JavaScript, gdy stosujesz
[async/await](/2018/05/asynchronicznosc-javascript-async-await.html). Wyrażenia Regularne są też
typem pierwszo-klasowym tzn. można je wstawiać bezpośrednio, nie jak w PHP wewnątrz ciągów znaków.

Inną ciekawą funkcją jest napisany przeze mnie Bookmarklet, który uruchamia Interpreter języka Scheme na
dowolnej stronie.

Jeśli wolisz bardziej zgodny ze standardem język Scheme (czytaj obsługujący kontynuacje i rekurencje ogonową),
możesz wypróbować projekt [BiwaScheme](https://www.biwascheme.org/).  Dodam, że terminal na stronie jest mojego
autorstwa, taki sam jak na stronie projektu LIPS.  Także logo projektu, jest moim dziełem.

Możesz porównać oba interpretery:

**LIPS**
```
lips> (! 100)
93326215443944152681699238856266700490715968264381621468592963895217599993229915608941463976156518286253697920827223758251185210916864000000000000000000000000
```

**BiwaScheme**
```
biwascheme> (! 100)
=> 9.33262154439441e+157
```

LIPS jest o wiele prostszy (jeśli chodzi o kod), dzięki czemu łatwo dodawać nowe funkcje i poprawki.
To samo w BiwaScheme jest problematyczne, ze względu na zastosowaną architekturę. A napisanie funkcji
ze specyfikacji, aby mieć w 100 procentach Scheme, jest całkiem możliwe w języku LIPS, tylko trzeba
dodać kontynuacje i optymalizacje rekurencji ogonowej. Duża część standardowej biblioteki LIPS to kod Scheme.


## Pisanie kodu

Jeśli chcesz wypróbować dialekt scheme podczas oglądania wykładów, to możesz skorzystać ze strony
[repl.it](https://repl.it/languages/scheme).  Strona korzysta z języka Scheme napisanego w
JavaScript (dokładnie interpreter BiwaScheme). Zapewne wiele razy przerwiesz oglądanie, żeby
wypróbować jakiś kod samemu. Jeśli nie to powinieneś chociaż spróbować.

Jeśli chcesz spróbować napisać i wypróbować jakiś program na swoim komputerze, to polecam dwa programy
[Kawa](https://www.gnu.org/software/kawa/index.html) (do której
swoja drogą też zrobiłem logo) oraz [Guile](https://en.wikipedia.org/wiki/GNU_Guile). Kawa to Scheme w języku
Java, a Guile jest w języku C. Guile jest fajny bo można go osadzić w aplikacji w C (jest biblioteka współdzielona,
która zawiera język). Natomiast Kawa-y można np. użyć do pisania aplikacji na
[Androida](https://www.gnu.org/software/kawa/Building-for-Android.html) (tak Lisp w telefonie).
Można także do tego celu użyć języka Clojure.

Oglądając wykłady Abelsona i Sussmana na Youtube możesz skorzystać z
[bookmarka](https://pl.wikipedia.org/wiki/Bookmarklet), który napisałem.
Dodaje on interaktywny interpreter języka Scheme do dowolnej strony (chyba że jest zabezpieczenie o nazwie
[CSP](https://en.wikipedia.org/wiki/Content_Security_Policy)). Link do bookmarka na stronie projektu
[LIPS Scheme](https://lips.js.org#bookmark). O ile np. strony GitHub, StackOverflow czy Google
są zabezpieczone przez CSP i tyle np. Wikipedia czy YouTube pozwalają na dowolne uruchamianie zewnętrznych
skryptów (takich jak zakładki z kodem).

Fajną funkcją YouTube jest to że nie przeładowuje strony przy otwieraniu filmów więc można odpalić
bookmark i mieć go cały czas dostępnego.

Tak wygląda interpreter języka Scheme na stronie YouTube:

![LIPS Scheme bookmark with YouTube video](/img/lips-scheme-bookmark.png)

Jeśli wolisz książki możesz np. skorzystać z darmowych pozycji na temat języka Scheme. Możesz po nie sięgnąć,
także gdy przebrniesz już przez wykłady lub w trakcie ich oglądania.

Kilka pozycji które udało mi się znaleźć (w języku angielskim):

* [Scheme WikiBook](https://en.wikibooks.org/wiki/Scheme_Programming)
* [sketchy LISP](https://archive.org/details/sketchy-lisp/page/n17/mode/2up)
* [Teach Yourself Scheme in Fixnum Days](https://ds26gte.github.io/tyscheme/)

## Co powinieneś poznać?

Powinieneś poznać takie koncepcje jak domknięcia leksykalne (ang. closures), funkcje jako typ pierwszo-klasowy
oraz pisanie funkcji wyższego rzędu (funkcje, które zwracają funkcje, albo pobierają je jako argument).

Wszystko to jest dostępne w języku JavaScript, który dużo zapożyczył z języka Scheme.

## Kolejny krok

Jak już masz to za sobą to znaczy, że znasz już trochę LISP-a, a dokładnie dialekt scheme. Teraz polecam
książki np. wspomniana Struktura i Interpretacja Programów Komputerowych. Nie jestem pewien czy można ją
jeszcze kupić po polsku ale jest
[dostępna w oryginale na stronie MIT](https://mitpress.mit.edu/sites/default/files/sicp/index.html).
Jak już dobrze pozasz język scheme to warto poznać Common LISP-a, możesz przeczytać mój
[kurs Common LISP-a](https://jcubic.pl/jakub-jankiewicz/lisp_tutorial.php), który kiedyś napisałem.
Warto sięgnąć po książkę [Practical Common Lisp](http://www.gigamonkeys.com/book/) (w oryginale dostępna
za darmo w wersji elektornicznej). Możesz też przeczytać książkę
["Successful Lisp: How to Understand and Use Common Lisp"](https://psg.com/~dlamkins/sl/contents.html), trzeba
się przedrzeć przez linki, ale na GDrive powinien być plik z książką (nie linkuje do GDrive, bo
strona może się zmienić).

## Makra

Jak już poznasz Common LISP-a lub Scheme to możesz zacząć naukę makr, chociaż możesz też po nie
sięgnąć już po obejrzeniu wykładów SICP. Makra to jest najlepsza rzecz jaka jest w języku LISP. Do
ich nauki polecam książkę [Let Over Lambda](https://letoverlambda.com/). Można też kupić wersje
papierową z [amazon.co.uk](https://www.amazon.co.uk/s?k=let+over+lambda&ref=nb_sb_noss). Osobiście
wole czytać książki fizyczne. Książka jest bardzo zaawansowana. Na stronie opisywana jako książka
hardcorowa, ale warto. Tylko muszę dodać, że jest poświęcona prawie w całości makrom.

Muszę też wymienić książkę, którą ostatnio udało mi się dostać w wersji papierowej, a mianowicie On LISP Paula
Grahama (jest to gość od książki
[Hakerzy i Malarze](http://helion.pl/view/12418M/hakmal.htm),
którą polecam. Jest też założycielem inkubatora "Y Combinator" dzięki, któremu mamy stronę
[Hacker news](https://news.ycombinator.com/)). Książka jest już dawno nie drukowana i można tylko kupić
jej wersje używaną za setki złotych, ale na szczęście autor udostępnił wersje elektroniczną. Można ją
wydrukować na lulu.com [tutaj artykuł](http://www.lurklurk.org/onlisp/onlisp.html) na podstawie, którego
udało mi się wydrukować swoją na [LuluXpress](https://xpress.lulu.com/) - koszt 15 dolarów wraz z wysyłką
(w Polsce chcieli za druk ok 200zł).

## Co dalej?

Teraz już tylko możesz dużo pisać i dużo czytać. Jak z każdym językiem. Chodzi głównie o kod. Chociaż
czytać można też artykuły. Z rzeczy które możesz jeszcze chcieć poznać to **kontynuacje** (których sam jeszcze
do końca nie rozumiem - chciałbym je dodać do swojej implementacji
[LISP-a w JavaScript](https://lips.js.org), ale nie jestem pewien czy znam je na tyle, aby je
zaimplementować). W Common Lisp jest jeszcze **CLOS**, czyli wbudowany system obiektowy.

Polecam też używanie edytora **GNU Emacs**, który jest częściowo napisany w języku Emacs LISP (w skrócie
ELisp) i można go rozszerzać za pomocą tego języka (fajną funkcją jest to że pod każdym skrótem klawiszowym,
nawet pojedynczym klawiszem, który wstawia literę, jest jakaś funkcja w języku ELisp, którą można dowolnie
zmienić, jest też dostępna pełna dokumentacja do każdej funkcji. A jak **budujesz ze źródła**, to możesz nawet
widzieć **kod źródłowy funkcji** samego edytora **wewnątrz edytora**).

Możesz też spróbować sam napisać interpreter tego języka. Ja sam napisałem taki w języku JavaScript, link
do tego projektu podałem wcześniej. Jeśli chciałbyś napisać własny interpreter Lispa. możesz zacząć
od tego artykułu:
[Parser S-Wyrażeń (języka LISP) w JavaScript](/2019/06/parser-jezyka-lisp-javascript.html).

*[SICP]: Structure and Interpretation of Computer Programs
*[CSP]: Content Security Policy
