---
layout: post
title:  "O czym pamiętać tworząc bibliotekę Open Source w JavaScript"
date:   2018-03-04 10:00:41+0100
categories:
tags:  biblioteki javascript open-source
author: jcubic
description: Pisząc nową bibliotekę Open Source w JavaScript warto pamiętać o kliku sprawach.
image:
  url: "/img/javascript-tools.jpg"
  alt: "Logos for JavaScript tools"
---

Jeśli masz zamiar napisać swoją własną bibliotekę Open Source w JavaScript, jest kilka rzeczy, o których
warto pamiętać.

<!-- more -->

## System Kontroli Wersji

Istnieje wiele systemów kontroli wersji, ale dzisiaj wszyscy chyba używają tylko gita. Jest to program napisany
przez Linusa Torvaldsa, tego od Linuxa. Napisany został z myślą o zarządzaniu jądrem Linuxa, który jest bardzo
złożonym projektem. Więc od samego początku był sprawdzany w boju. Jest to system rozproszony tzn. że każdy
użytkownik posiada pełną kopie repozytorium (starszy svn miał jeden centralny serwer) więc można go mieć
umieszczonego na wielu serwerach. Dodatkową fajną opcją jest to że można na nim pracować lokalnie tzn. nie
wgrywać swoich zmian na serwer.

Git posiada aplikacje wiersza poleceń, ja korzystam tylko z niej, ale istnieją także wtyczki do popularnych
narzędzi programistycznych IDE.

Jeśli chodzi o komendy to najczęstsze to:

* git clone - tworzy kopie lokalną repozytorium,
* git commit - dodaje zmiany,
* git push - wrzuca commity na serwer,
* git pull - zaciąga zmiany z serwera,
* git status - wyświetla status,
* git diff - wyświetla nie zakomitowane zmiany.

Jeśli chcesz się zapoznać z systemem kontroli wersji Git polecam
[oficjalną książkę na jego temat](https://git-scm.com/book/pl/v2).

Do utrzymywania repozytorium Gita, można korzystać z takich serwisów jak:

* [GitHub](https://github.com) chyba najbardziej popularny,
* [Bitbucket](https://bitbucket.org/),
* [GitLab](https://about.gitlab.com/),
* Był jeszcze [Gitorious](http://www.gitorious.com/), ale został wykupiony przez GitLab.

Fajną funkcją GitHuba jest wspominanie issues-ów za pomocą commitów (a dokładnie za pomocą teksu wiadomości).

Przy zgłaszaniu bugów, nie musisz czekać na użytkowników, ale sam je dodawać. Co jest szczególnie ważne jest przy
skomplikowanych projektach. Ale nawet dla małych, może być pomocne w śledzeniu błędów i nowych
funkcji. Warto jest nie zamykać też issues-ów, do puki nie wyda się nowej wersji, aby użytkownicy widzieli
dany błąd i nie zgłaszali go jeszcze raz mimo, że poprawka jest już w repozytorium.

Jeśli chodzi o gita to ja osobiście korzystam z takiego flow, mam dwa branch-e master (jest od samego początku)
oraz devel. Poprawki i nowe funkcje idą do branch-a devel, jak uzbiera się klika błędów, albo funkcji
(jeśli chodzi o błędy to do wersji może wejść też jeden), to merge-uje do mastera, czyli wywołuje:

{% highlight bash %}
git checkout master
git merge devel --no-ff -m "Merge with devel"
git push
{% endhighlight %}

`--no-ff` określa, aby zawsze był tworzony nowy commit, ponieważ git czasami wykonuje tzw. fast forward.

Następnie podbijam wersje, jeśli są same bugi to wersja 1.1.0 zamienia się na 1.1.1. Natomiast jeśli
wchodzą nowe funkcje to wersja to 1.2.0. Jeśli są zmiany, które zmieniają API to nową wersją będzie
2.0.0. Jest to konwencja `BRAKING.FEATURE.BUG`.

## Licencja

Licencja jest bardzo ważna, określa kto i jak może korzystać z tworzonej biblioteki. Osobiście
korzystam z [Licencji MIT](https://opensource.org/licenses/MIT), ale nic nie stoi na przeszkodzie
aby wybrać inną. Listę różnych licencji możesz znaleźć na stronie
[opensource.org](https://opensource.org/licenses/alphabetical).  Fajną funkcją GitHuba jest
dodawanie licencji z listy, można to zrobić przy tworzeniu repozytorium albo dodając nowy plik. Nic
nie stoi jednak na przeszkodzie, aby dodać ją normalnie za pomocą `git commit`.  Przy dodawaniu
poprzez stronę internetową, możemy też od razu zobaczyć, na co dana licencja pozwala.

## Plik README

Plik README określa pierwszą stronę, którą zobaczy osoba oglądająca daną bibliotekę. Ważne jest aby
plik zawierał opis biblioteki, jak jej używać oraz licencje. Można też dodać listę osób które
pomogły przy jest pisaniu (dodawały swoje poprawki poprzez pull request-y), ale to dopiero
jak twoja biblioteka będzie na tyle popularna, aby użytkownicy chcieli ją poprawiać.

Do pliku możesz dodać rozszerzenie `md`, dzięki temu, będziesz w nim mógł zapisywać formatowanie
zgodnie z językiem [Markdown](https://guides.github.com/features/mastering-markdown/). GitHub ma
dodatkowe funkcje dodane do języka jak np. dodawanie URLi dla użytkowników czy issues-ów oraz bardzo
fajne podświetlanie składni.

## Plik CHANGELOG

Plik CHANGELOG to zapis zmian w każdej nowej wersji. Warto dla każdej nowej wersji tworzyć taga w
git-cie, oraz dodawać tzw. release note, wraz z poprawkami i funkcjami, które weszły do tej
wersji. (zazwyczaj kopiowane z pliku CHANGELOG). Tak jak w przypadku README możesz dodać rozszerzenie `md`.

Warto na samej górze mieć wersje Next, która jest uaktualniana przy każdej zmianie kodu. Przykładowy plik może
wyglądać tak:

```
## Next
### Bug fixes
* fix error in serialization [#1](https://github.com/<USER>/<REPO>/issues/1)

## 0.1.0
* First version
```

## Managery Pakietów

Jeśli chcesz aby twoja biblioteka była używana, warto dodać ją do [NPM-a](https://www.npmjs.com/),
czyli managera pakietów node. NPM to tak naprawdę dwa różne elementy, repozytorium pakietów oraz
manager do zarządzania zależnościami pod postacią komendy wiersz poleceń. Tą komendę można zastąpić
nowszym programem, bardzo polecanym managerem Yarn, utworzonym przez Facebooka.

NPM i Yarn to nie tylko publikowanie nowych projektów, ale także możliwość skorzystania z ogromnej liczby
pakietów więc twoja biblioteka nie musi wszystkich funkcji pisać od zera.

Jeśli chodzi o managera NPM to aby z niego skorzystać (jeszcze nie miałem okazji korzystać z yarn) należy
z wiersza poleceń wykonać (oczywiście po instalacji NPM-a).

```
npm init
```

Następnie należy odpowiedzieć na parę pytań. Musze dodać, że najpierw warto utworzyć repozytorium gita,
w przeciwnym razie, będzie trzeba ręcznie wpisywać adres repozytorium do pliku package.json (jest to plik
w którym jest cała konfiguracja pakietu).

Jeśli chcesz dodać jakąś nową zależność (nowy pakiet, o którym gdzieś przeczytałeś), należy wykonać
polecenie `npm install {PAKIET}` z opcją `--save` lub `--save-dev`, które określają, czy dany pakiet
ma być tylko dla programistów, czy dla wszystkich użytkowników. Jeśli ktoś pobierze bibliotekę za
pomocą gita, będzie musiał wykonać `npm install` (chyba że dodacie katalog node_modules do repo, ale
nie polecam), który zainstaluje wszystkie dodane wcześniej zależności (łącznie z tymi z
`--save-dev`). W przypadku, gdy ktoś zainstaluje bibliotekę za pomocą NPM-a albo yarn, wszystkie
zależności będą zainstalowane przez manager pakietów ale tylko te z `--save`.

Aby opublikować pakiet należy się zarejestrować na stronie [npmjs.org](https://www.npmjs.com/) następnie
z wiersza poleceń wywołać:

```
npm login
```

Po weryfikacji zostanie utworzony plik `.npmrc`, w katalogu głównym użytkownika, który będzie służył do
uwierzytelniania przy wykonywaniu polecenia `npm publish`.

Jeśli wybierzesz już nazwę biblioteki, ale jest ona zajęta, to możesz utworzyć użyć zasięgu
(ang. scope), którym może być organizacja, ta opcja jest płatna, ale może nim być też nazwa
użytkownika. Możesz ręcznie edytować plik package.json aby dodać `@użytkownik/<nazwa>` albo przy
inicjalizacji wykonać:

```
npm init --scope=użytkownik
```

aby opublikować taki pakiet należy użyć polecenia:

```
npm publish --access=public
```

inaczej npm zwróci błąd, że nie masz prawa tworzenia prywatnego repozytorium.

Istnieje także inny system pakietów o nazwie [Bower](https://bower.io/), który w odróżnieniu od NPM, który
był pierwotnie przeznaczony dla node.js, przeznaczony jest dla przeglądarek. Tworząc pakiet warto także
dodać go do bowera. Chociaż bower chyba jest już nie aktualny ponieważ instalacja wyświetla komunikat
o przedawnieniu (ang. deprecated warning).

Aby dodać pakiet do bowera, nie trzeba się nigdzie logować wystarczy, że masz zainstalowanego bowera.
Rejestracja projektu polega na wywołaniu:

```
bower register <name> <url>
```

Gdzie url to ścieżka do repozytorium gita. Aby zainstalować bowera można skorzystać z npm.

```
npm install -g bower
```

opcja `-g` oznacza że pakiet będzie zainstalowany globalnie. Żeby bibliotekę można było zainstalować za pomocą
bowera, potrzebny jest jeszcze plik bower.json, można go utworzyć ręczenie, albo za pomocą polecenia:

```
bower init
```

Musisz też pamiętać, że podając projekt w npm, publikujesz także twój adres email (możesz spróbować usunąć adres
z pola author w pliku package.json, ale nie wiem czy to działa). Tak samo jest z GitHubem,
przy ustawieniach domyślnych, chociaż jest możliwość ukrycia tego adresu.

## Plik .gitignore

Jest to plik, który wskazuje co ma być ignorowane przez gita, ale też przez npm. Należy dodać do
niego katalog managera pakietów, czyli `node_modules` oraz `bower_components`, pliki tymczasowe czy
pliki z twojego IDE, które nie powinny się znaleźć w repo. O ile ignorowanie przez gita nie jest
takie ważne, będzie tylko wyświetlany komunikat, że są nie dodane pliku, gdy wykonamy polecenie `git
status`, ale dla npm, już jest to bardzo ważne, ponieważ npm dodaje wszystkie pliki z danego
katalogu a nie tylko te, które są w git-cie.

Plik .gitignore i jego odpowiednik .npmignore (używany jeśli nie chcesz aby np. pliku źródłowe,
czy pliki służące do budowania projektu, wylądowały w npm).  powinien zwierać listę plików albo
[globów](https://pl.wikipedia.org/wiki/Glob_(programowanie)), czyli wzorców plików.

## Lint

Lintowanie to proces sprawdzania kodu lub pliku JSON-a według odpowiednich reguł. Tak na prawdę jest
on uniwersalny, a wywodzi się z [pierwotnego Unixa i języka C](https://pl.wikipedia.org/wiki/Lint), gdzie był
procesem, który zawsze wywoływany był przed kompilacją). Dzisiaj proces ten oznacza
[statyczną analizę](https://pl.wikipedia.org/wiki/Testy_statyczne) kodu.

Jeśli chodzi o mnie, to do analizy statycznej JavaScript używam narzędzia
[ESLint](https://eslint.org), który jest bardzo rozbudowany i masę opcji, które można włączać i
wyłączać. Przed ESLint popularnymi narzędziami były [JSHint](http://jshint.com/) oraz pierwszy
linter, czyli [JSLint](http://jslint.com/), napisany przez Douglasa Crockforda, tego od formatu
JSON. Nie polecam tego ostatniego, ma zaszyte w kodzie reguły, których używał autor, który wspomniał
w jednej z prelekcji, że JSLint urazi twoje uczucia.

Aby zainstalować ESLint wystarczy z wiersza poleceń wykonać:

```
npm install --save-dev eslint
```

Następnie trzeba go skonfigurować, najprościej jest dodać reguły w pliku package.json, można także skorzystać
z wizarda za pomocą polecenia:

```
eslint --init
```

Dobrze jest przejrzeć wszystkie opcje narzędzia ESLint i włączyć te, które chcemy. Nie będziesz raczej
musiał ich sprawdzać przy następnych projekcie i tylko skopiujesz je do nowego, tylko ewentualnie
wyłączysz jakąś opcje, która nie jest potrzebna.


## Testy jednostkowe

Testy biblioteki są bardzo ważne, dzięki nim masz większą pewność, że biblioteka działa poprawnie.
Ważne jest także przy dodawaniu nowych funkcji i naprawianiu błędów. Możesz wtedy mieć większą pewność,
że nowe zmiany nie popsuły tego co już zostało przetestowane. Przy poprawianiu błędów warto najpierw
napisać test, który się nie powiedzie i dopiero potem zająć się naprawianiem błędu. Jest to tzw. TDD (ang.
Test Driven Development), który uważam dobrze się sprawdza szczególnie przy naprawianiu błędów.

Do testów potrzebna jest biblioteka/framework do testów. Ja korzystam z
[Jasmine](https://jasmine.github.io/) a ostatnio z
[Jest](https://facebook.github.io/jest/) od Facebooka (jest to skrót od jester - błazen, trochę niefortunna
nazwa jeśli chodzi o język polski).


By zainstalować `Jest` należy wykonać:

```
npm install --save-dev jest
```

Pisanie testów jest bardzo proste. Można utworzyć katalog spec i w nim utworzyć nowy plik (może być
ich także więcej niż jeden) on nazwie `nazwa.spec.js`. Jeśli twoja biblioteka korzysta z UMD (opisanego
dalej w artykule) to kod może wyglądać tak:

{% highlight javascript %}
import {foo, bar} from '../src/main';

describe('foo', function() {
    it('should return string "foo"', function() {
        expect(foo()).toEqual('foo');
    });
    it('should return string with appended "foo"', function() {
        expect(foo('lorem')).toEqual('lorem foo');
        expect(foo('ipsum')).toEqual('ipsum foo');
    });
});
describe('bar', function() {
    it('should return array with "bar"', function() {
        expect(bar()).toEqual(['bar']);
    });
    it('should return array with two elements: argument and "bar"', function() {
        expect(bar('lorem')).toEqual(['lorem', 'bar']);
        expect(bar('ipsum')).toEqual(['ipsum', 'bar']);
    });
});
{% endhighlight %}


Pełną dokomentacje na temat dostępnych funkcji w bibliotece `Jest` można znaleźć na
[stronie projektu](https://facebook.github.io/jest/). Ciekawe jest, że w dokumentacji nie ma uwzględnionej
funkcji `it` tylko `test` ale powyższy kod działa. `it` jest to funkcja z `Jasmine`, pewnie chcieli być
bardziej kompatybilni.

Aby uruchomić test wystarczy wykonać:

./node_module/.bin/jest

można też dodać w package.json:

{% highlight javascript %}
{
   // ...
   "scripts": {
      "test": "jest"
   }
   // ...
}
{% endhighlight %}

a następnie wystarczy wykonać:

```
npm test
```

## Testy pokrycia

Code coverage jest to test, który wskazuje ile kodu jest przetestowane. Polega ona na tym, że sprawdzane jest
czy dana linijka albo odgałęzienie (ang. branch) było uruchomione w trakcie testów.

Aby włączyć testy pokrycia za pomocą biblioteki `Jest` wystarczy dodać plik `jest.config.json`
przedstawiony poniżej:

{% highlight javascript %}
module.exports = {
    collectCoverage: true
};
{% endhighlight %}

Fajną cechą `Jest` jest to, że informacje o pokryciu testami wykonywane są w samym `Jest`. W przypadku
Jasmine trzeba odpalać testy dwa razy, jeśli chcesz mieć więcej informacji o wykonywanych testach.
Raz przez jasmine i raz przez [istanbul](https://github.com/gotwarlost/istanbul).

## System budowania plików

Istnieje kilka narzędzi, które można użyć w celu budowania projektu. Są nimi np.
[grunt](https://gruntjs.com/) czy [gulp](https://gulpjs.com/). Budowanie, w przypadku projektu w
JavaScript, to np. łączenie plików i minifikacja w celu utworzenia jednego wynikowego pliku,
uruchamianie testów, analiza statyczna kodu czy konwertowanie kodu źródłowego (ang. transpiling).

Ja osobiście nie korzystam z tych narzędzi i do swoich projektów Open Source używam programu
[make](https://pl.wikipedia.org/wiki/Make). Jest to bardzo proste narzędzie znane chyba każdemu kto korzysta
z jakiegoś Unixa, ale dostępne jest także pod Windowsa. Program `make` opiera się na plikach `Makefile`,
które są składnią podobne do CSS tzn. są reguły i operacje w ramach tych reguł. Regułą może być reguła fikcyjna
tzw. `PHONY`, która zawsze jest wykonywana. Albo reguła, która zależy od jakiegoś innego pliku, która jest
wykonywana, gdy ten plik się zmieni. Operacje to zwykłe komendy wiersza poleceń. Jeśli któraś z komend się
nie powiedzie (zwróci kod błędu) to wtedy nie powiedzie się też komenda `make`. Ważna cechą `make` są znaki
tabulacji, które określają przynależność komend od reguł. `make` opiera się na czasie modyfikacji plików.

Jeśli masz jednak więcej niż jeden plik, których używasz jak moduły lepszym rozwiązaniem będzie np.
[Rollup](https://rollupjs.org/guide/en).
Jest też [Webpack](https://webpack.js.org/) ale raczej
[stosuje się go do aplikacji SPA, a nie do bibliotek](http://u.jcubic.pl/n461y26).

Jeśli chcesz użyć Rollup w procesie budowania projektu, powinieneś go zainstalować lokalnie bez opcji `-g`.

```
npm install --save-dev rollup
```

I używać tako `./node_modules/.bin/rollup`.


Pisząc aplikacje warto korzystać z ES6, czyli nowej wersji JavaScript (chociaż są już nowsze) nawet
jeśli przeglądarka IE go nie obsługuje. Jeśli chcecie, aby wasza biblioteka działała w IE możecie
skorzystać z narzędzia [Babel](https://babeljs.io/), które skonwertuje kod twojej biblioteki do ES5.

Aby zainstalować narzędzie Babel należy wykonać:

```
npm install --save-dev babel babel-preset-env
```

Potem utworzyć plik `.babelrc` w katalogu głównym, który wygląda tak:

{% highlight javascript %}
{
  "presets": ["env"]
}
{% endhighlight %}

Warto także zatroszczyć się o to tworzenie plików README oraz package.json z odpowiednią wersją, aby
nie trzeba było zmieniać wersji ręcznie. W ostatnim projekcie utworzyłem sobie katalog `templates`
(wcześniej miałem pliki z rozszerzeniem `.in` od input), w którym zapisałem pliki:

* README.md
* Makefile
* package.json

w pliku `template/Makefile` mam coś takiego:

{% highlight makefile tabsize=4 %}
{% raw %}
.PHONY: publish test lint

VERSION={{VERSION}}

GIT=git
SED=sed
RM=rm
TEST=test
ESLINT=./node_modules/.bin/eslint
JEST=./node_modules/.bin/jest
UGLIFY=./node_modules/.bin/uglifyjs
BABEL=./node_modules/.bin/babel

ALL: Makefile .$(VERSION) dist/nazwa.js dist/nazwa.min.js README.md pacakge.json

dist/nazwa.js: src/nazwa.js .$(VERSION)
	$(GIT) branch | grep '* devel' > /dev/null && $(SED) -e "s/{{VER}}/DEV/g" -e \
    "s/{{DATE}}/$(DATE)/g" src/nazwa.js  > dist/nazwa.tmp.js || $(SED) -e "s/{{VER}}/$(VERSION)/g" \
    -e "s/{{DATE}}/$(DATE)/g" src/nazwa.js > dist/nazwa.tmp.js
	$(BABEL) dist/nazwa.tmp.js > dist/nazwa.js
	$(RM) dist/nazwa.tmp.js

dist/lips.min.js: dist/nazwa.js
	$(UGLIFY) -o dist/nazwa.min.js --comments --mangle -- dist/nazwa.js

Makefile: templates/Makefile
	$(SED) -e "s/{{VER""SION}}/"$(VERSION)"/" templates/Makefile > Makefile

README.md: templates/README.md .$(VERSION)
	$(GIT) branch | grep '* devel' > /dev/null && $(SED) -e "s/{{VER}}/DEV/g" -e \
    "s/{{BRANCH}}/$(BRANCH)/g" < templates/README.md > README.md || $(SED) -e \
    "s/{{VER}}/$(VERSION)/g" -e "s/{{BRANCH}}/$(BRANCH)/g" < templates/README.md > README.md

package.json: templates/package.json .$(VERSION)
	$(SED) -e "s/{{VER}}/"$(VERSION)"/" templates/package.json > package.json

.$(VERSION): Makefile
	# przy zmianie pliku Makefile uaktualnią się wszystkie reguły, które zależą od pliku VERSION
	touch .$(VERSION)

test:
    $(JEST)

publish:
	npm publish --access=public

lint:
	$(ESLINT) src/nazwa.js
{% endraw %}
{% endhighlight %}


Plik `.$(VERSION)` jest potrzebny. W przeciwnym razie, przy zmianie wersji, nie zbuduje się projekt.

Pliki `README.md` oraz `package.json` powinny mięć wpisane ciągi znaków `{{VER}}`, w każdym miejscu,
gdzie powinna pojawić się wersja. Natomiast plik `src/nazwa.js`, może mieć dodatkowo `{{DATE}}`.

Aby zbudować projekt wystarczy wywołać:

```
make
```

Przy pierwszym uruchomieniu będziesz musiał skopiować plik `Makefile` z katalogu `template` do
katalogu głównego i wpisać ręcznie number pierwszej wersji, ale już po każdej zmianie pliku `Makefile` z
katalogu `template`, plik uaktualni się automatycznie.

Ważne jest, aby przy każdej instalacji za pomocą npm, uaktualniać plik `template/package.json` inaczej nasze
pakiety zostaną usunięte przy zmianie wersji. Wystarczy wykonać

```
cp package.json template/package.json
```

i wstawić `{{VER}}` w miejsce wersji.

Jeśli chcesz przeczytać więcej o programie make szczególnie z JavaScript możesz przeczytać artykuł
["The Lost Art of the Makefile"](http://www.olioapps.com/blog/the-lost-art-of-the-makefile/).

Korzystam także z prostego skryptu do wyświetlania aktualnej i podbijania wersji, można go też użyć
do utworzenie pierwszej wersji, aby nie trzeba było ręczenie kopiować pliku `Makefile`.

{% highlight bash %}
#!/bin/bash

# Display current version or update version if used with version as argument

VERSION=`grep VERSION= Makefile | sed -e 's/VERSION=\(.*\)/\1/'`
if [ -z "$1" ]; then
    echo $VERSION
elif [ "$1" != "$VERSION" ]; then
    sed -e "s/{{VERSION}}/"$1"/" templates/Makefile > Makefile
fi
{% endhighlight %}

Po zmianie wersji zawsze należy przebudować projekt wiec, zazwyczaj wykonuje (już po złączeniu zmian
z branch-em devel).

{% highlight bash %}
version # wyświetla aktualną wersje
version 0.2.0
make
git commit -am "version 0.2.0"
git push
git tag 0.2.0
git push --tags
make publish
{% endhighlight %}

Można by to uprościć i dać cześć tych poleceń do skryptu `version`, ale wolę robić to ręczenie. Na wszelki
wypadek, jakbym musiał jeszcze dodawać jakieś zmiany do mastera.

Jedna rzecz na którą trzeba uważać przy takiej konfiguracji to konflikty jeśli nad projektem pracuje więcej
niż jedna osoba (o czym się niedawno przekonałem) przy każdym commit-cie zmienia się Data. Jeśli ktoś tworzy
Pull Request-a, a ty w tym czasie budowałeś projekt, to osoba która tworzyła PR będzie miała konflikt.

Aby tego uniknąć można wcale nie budować projektu pracując na branch-u devel i tylko wykonywać testy oraz
lintowanie.

## Wersja i notka o autorze w każdym pliku źródłowym

Jeśli masz jeden plik, to może on wyglądać tak:

{% highlight javascript %}
/**@license
 * My New Library - version {{VER}}
 *
 * Copyright (c) 2018 Imię Nazwisko <adres strony>
 * Released under the LICENCJA license
 *
 * build: {{DATE}}
 */
"use strict";
{% endhighlight %}

`"use strict"` jest ważne, jeśli będziesz korzystać z narzędzia babel, inaczej twoja notka o autorze
zostanie umieszczona trochę niżej w kodzie, a nie na samej górze. (znalezione na GitHubie w komentarzu,
ale Z niewiadomych przyczyn, jak dodałem łączenie plików za pomocą Rollup-a, to przestało to działać)
`@license` jest także ważne, inaczej twoja notka zostanie usunięta przez UglifyJS.

Jeśli masz więcej niż jeden plik, korzystasz z modułów i Rollup, to możesz dodać zwykły komentarz np.:

{% highlight javascript %}
/*
 * Part of My New Library
 *
 * Copyright (c) 2018 Imię Nazwisko <adres strony>
 * Released under the LICENCJA license
 *
 */
{% endhighlight %}

a ten z `@license` wstawić do osobnego pliku o łączyć razem w wynikowy plik. Mój plik Makefile (a właściwie
jego część) dla takiej konfiguracji wyglądałby tak:

{% highlight makefile %}
# dodatkowe zmienne
CAT=cat
ROLLUP=./node_modules/.bin/rollup
PERL=/usr/bin/env perl

dist/lib.js: src/main.js .$(VERSION)
    # nazwa --name to jest przestrzeń nazw, które będzie użyta w przeglądarce
	$(ROLLUP) src/main.js -f umd --name 'lib' --o dist/bundle.js
	$(BABEL) dist/bundle.js > dist/babel.js
    # usunięcie komentarzy z każdego z plików
	$(PERL) -p0i -e 's#/\*.*?\*/##sg' dist/babel.js
	$(CAT) src/banner.js dist/babel.js | $(SED) -e '/^\s*$$/d' > dist/lib.js
    # dodanie wersji DEV albo tej z Makefile
	$(GIT) branch | grep '* devel' > /dev/null && $(SED) -i -e "s/{{VER}}/DEV/g" -e \
	"s/{{DATE}}/$(DATE)/g" dist/lib.js || $(SED) -i -e \
	"s/{{VER}}/$(VERSION)/g" -e "s/{{DATE}}/$(DATE)/g" dist/lib.js
    # usunięcie plików pośrednich
	$(RM) dist/bundle.js dist/babel.js
{% endhighlight %}

## UMD

[UMD](/2017/07/uniwersalne-biblioteki-javascript.html) to skrót od angielskiego Universal Module
Definition.  Jest to sposób zapisu kodu, który będzie działał w przeglądarce jak i w node.js, ale
także poprzez bibliotekę [requirejs](http://www.requirejs.org/).

Kod może wyglądać tak:

{% highlight javascript %}
(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define([], function() {
            return (root.nazwa = factory());
        });
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory();
    } else {
        root.nazwa = factory();
    }
})(typeof self !== 'undefined' ? self : this, function(undefined) {

    /* twój kod */

    return {
       version: '{% raw %}{{VER}}{% endraw %}',
       /* inne metody API */
    };
});
{% endhighlight %}

Jeśli korzystasz z Rollup, to możesz go wygenerować automatycznie za pomocą opcji `-f umd`.

## Ciągła integracja

[Ciągła integracja](https://pl.wikipedia.org/wiki/Ci%C4%85g%C5%82a_integracja) z angielskiego
Continuous integration, jest systemem, który po każdej zmianie w repozytorium (ang. commit) odpali
nasz proces budowania projektu (zwłaszcza testy) i powiadomi nas gdy coś się nie powiedzie. W pracy
korzystam z Systemu Jenkins, natomiast przy projektach Open Source korzystam z usługi o nazwie
[TravisCI](https://travis-ci.org), która dostępna jest za darmo dla projektów Open Source.

Aby się zarejestrować wystarczy konto na GitHubie (logowanie jest przez Githuba). Następnie należy
włączyć projekt.  TravisCI włączy odpowiednie funkcje dla naszego projektu na GitHubie, aby się
wywoływał przy każdej zmianie.  (jest to realizowana za pomocą funkcji GitHub Hooks).

Po dodaniu projektu wystarczy utworzyć plik .travis.yml i dodać np. coś takiego:

{% highlight yaml %}
language: node_js
node_js:
  - "node"
install:
  - npm install
script:
  - make lint
  - make test
after_script:
  - make coveralls
{% endhighlight %}

Ostatnia linijka to publikowanie w usłudze [Coveralls](https://coveralls.io/), która umożliwia
śledzenie zmian testów pokrycia. Tak jak Travis dostępna jest za darmo dla projektów Open
Source. Rejestracja i konfiguracja, także wygląda podobnie jak w przypadku Tavisa.  Logujemy się
przez Githuba i dodajemy nasz projekt.

Wpis w pliku Makefile dla `make coveralls` wygląda tak

{% highlight makefile tabsize=4 %}
COVERALLS=./node_modules/.bin/coveralls

coveralls:
	cat ./coverage/lcov.info | $(COVERALLS)
{% endhighlight %}


Można też dodać coveralls to sekcji `.PHONY:`. Plik `coverage/lcov.info` jest generowany przez `jest`.

Aby skorzystać z polecenia coveralls, należy zainstalować odpowiedni moduł z npm:

```
npm install coveralls --save-dev
```

Ważna rzecz, `coveralls` nie działa lokalnie i trzeba go odpalać z travisa. Nie wiem czy to błąd czy feature.


## Strona internetowa

Ostatnią rzeczą jest strona internetowa projektu. Jeśli nie masz własnej domeny, możesz skorzystać z
GitHub pages. Jest to usługa, która udostępnia hosting plików statycznych. Można wgrywać pliki html, css i js,
ale także stronę napisaną w [Jekyll](https://jekyllrb.com/).
[Moja strona](https://github.com/jcubic/jcubic.pl) korzysta z Jekyll-a, chociaż nie korzysta z Github pages.

Informacje o tym jak skorzystać z Jekyll-a możesz przeczytać, w języku angielskim, na tej
[stronie](https://help.github.com/articles/using-jekyll-as-a-static-site-generator-with-github-pages/).

Aby utworzyć stronę, musisz utworzyć nowe repozytorium `użytkownik.github.io` (użytkownik musi być
taki sam jak twoja nazwa użytkownika), która wygeneruje nową domenę o takiej samej nazwie, możesz
dodać tam swoją stronę główną np. z listą swoich projektów i linkami do innych portali takich jak
np. [twitter](https://twitter.com/) czy [codepen](https://codepen.io/).

Następnie dla danego projektu musisz utworzyć branch o nazwie `gh-pages`, w którym powinien
znajdować się plik index.html oraz inne statyczne pliki tak ja w przypadku repozytorium. Aby nie
trzeba było uaktualniać tego branch-a za każdym razem jak udostępniasz nową wersją, a gdy chcesz
zrobić demko biblioteki, to możesz zasysać pliki z rawgit (aby uzyskać link, można pobrać wersje raw
pliku z GitHuba, a następnie zmienić domenę na rawgit.com), pobranie pliku bezpośrednio z GitHuba
nie działa ponieważ ustawia on zły `Content-Type` dla plików. Można też pobrać pliki z
[unpkg.com](https://unpkg.com), który pobiera dane z NPM-a. Możesz też dodać te linki do README.

## Odznaki w README

Odznaki to bardzo fajny dodatek. Dla potencjalnego użytkownika, mogą one wskazywać, że projekt jest
wartościowy i warto go wypróbować. Jeśli będziesz już publikował wersje do npm-a, budował za pomocą
Travisa i używał Coveralls, to możesz już zamieścić 3 odznaki.

```
{% raw %}
[![npm](https://img.shields.io/badge/npm-{{VER}}-blue.svg)](https://www.npmjs.com/package/open-source-library)
[![travis](https://travis-ci.org/jcubic/open-source-library.svg?branch={{BRANCH}})](https://travis-ci.org/jcubic/open-source-library)
[![Coverage Status](https://coveralls.io/repos/github/jcubic/open-source-library/badge.svg?branch={{BRANCH}})](https://coveralls.io/github/jcubic/open-source-library?branch={{BRANCH}})
{% endraw %}
```

Wyglądają one tak:

[![npm](https://img.shields.io/badge/npm-0.1.0-blue.svg)](https://www.npmjs.com/package/open-source-library)
[![travis](https://travis-ci.org/jcubic/open-source-library.svg?branch=master)](https://travis-ci.org/jcubic/open-source-library)
[![Coverage Status](https://coveralls.io/repos/github/jcubic/open-source-library/badge.svg?branch=master)](https://coveralls.io/github/jcubic/open-source-library?branch=master)

Domyślenie GitHub cache-uje obrazki, więc jeśli chcesz, aby odznaka do testów pokrycia jak i do
budowania pakietu była uaktualniana po zmianie, możesz do odznaki Travisa dodać hash commit-u a do
Coveralls sumę kontrolną pliku testów (możesz też połączyć wszystkie pliku razem i z nich utworzyć
sumę jeśli masz więcej niż jeden plik).

Jeśli jesteś zainteresowany taką metodą budowania projektu, przygotowałem repozytorium na GitHubie z
ustawionym Travisem, Coveralls oraz GitHub pages wraz z pakietem na npm o nazwie
[open-source-library](https://github.com/jcubic/open-source-library), razem z wszystkimi omówionymi
elementami. Pakiet NPM możesz znaleźć
[tutaj](https://www.npmjs.com/package/open-source-library).

