---
layout: post
title:  "Jak dodać usługę Coveralls do projektu Open Source?"
date:   2019-03-03 11:49:37+0100
categories:
tags: javascript biblioteki open-source
author: jcubic
description: Jak dodać ciągłą integracje (Continuous Integration) i licznik pokrycia kodu testami, do projektu Open Source na GitHubie.
image:
  url: "/img/coveralls.png"
  alt: "Grafika z Logotypem coveralls oraz etykietami GitLab or BitBucket"
  width: 800
  height: 500
related:
  -
    name: "O czym pamiętać tworząc bibliotekę Open Source w JavaScript"
    url: "/2018/03/o-czym-pamietac-tworzac-biblioteke-open-source.html"
---

Ten wpis został zainspirowany przez [Piotra Kowalskiego czyli @piecioshka](https://piecioshka.pl/)
i jego projekt na GitHubie czyli [simple-data-table](https://github.com/piecioshka/simple-data-table).
W listopadzie zeszłego roku dodał Coveralls, ale pojawiło się tylko `coverage: unknown`.
Pisałem o tym już, w zeszłym roku, we wpisie
[O czym pamiętać tworząc bibliotekę Open Source w JavaScript](/2018/03/o-czym-pamietac-tworzac-biblioteke-open-source.html), ale postanowiłem odświeżyć te informacje i podać więcej szczegółów.
Jeśli jesteś zainteresowany jak dodać licznik pokrycia kodu testami oraz ciągłą integrację, do swojego
projektu Open Source (o Otwartym Kodzie Źródłowym), czytaj dalej.

<!-- more -->

### Co to jest Code Coverage?

Po polsku to pokrycie kodu źródłowego testami. Działa to tak, że program, który testuje
nasz kod, **uruchamia testy jednostkowe** (lub integracyjne, które można odpalić z poziomy np. node.js)
i jeśli dany test uruchamiając kod, **odwiedzi daną linijkę kodu**, to znaczy, że jest ona **pokryta
testami**. I tak cały kod. Licząc procentowo ile linii było pokrytych, do liczby wszystkich
linii kodu (pomijając komentarze) otrzymujemy pojedynczą liczbę, czyli procent pokrycia kodu testami.

Istnieje także **pokrycie odgałęzień** (ang. branch coverage), które **działa bardziej dokładnie**,
np. sprawdza czy wszystkie warunki `&&` i `||` się wywołują.

Najpopularniejszą biblioteką, która sprawdza test coverage jest biblioteka
[**Istanbul**](https://istanbul.js.org/). Działa prawdopodobnie tak, że dodawany jest **specjalny dodatkowy
kod**, do testowanego kodu źródłowego, dzięki temu wiadomo czy linia się wywołała czy.

Do pisania testów jednostkowych w JavaScript, najlepiej użyć narzędzia
[**jest od Facebooka**](https://jestjs.io/), jego fajną funkcją jest to, że **code coverage**, można uzyskać
(dzięki Istanbul) **w jednym przebiegu** testów, jednocześnie ze statystykami poszczególnych testów.

### Co to jest Travis?

TravisCI jest to **usługa ciągłej integracji** (ang. Continuous integration) dostępna pod adresem
[travis-ci.org](https://travis-ci.org) (jest też druga wersja w domenie .com). W skrócie można powiedzieć, że
jest to taki **bot dla GitHuba**, który wykonuje nasz kod (który jest w repozytorium) przy każdej zmianie na
każdej gałęzi (ang. each commit per branch). Działa to tak, że logujemy się do Travisa za pomocą naszego konta
na GutHubie dajemy mu prawo modyfikacji repozytoriów i przy dodawaniu repo za pomocą interfejsu **Travisa,
doda on WebHooka**, wskazując na siebie. **Co to jest WebHook?** Jest to coś jak zdarzenie w przeglądarce
tylko zamiast `onClick` jest `onCommit`, a zamiast funkcji, która się odpala jest url, do którego zostanie
wysłane zapytanie HTTP.

Co dalej robi Travis? Przy każdym odpalenie WebHooka, Travis **stawia wirtualne środowisko** (można wybrać
system operacyjny, ja używam Linuxa i odpalam testy tylko raz) instaluje potrzebne narzędzia (dla projektów
JavaScript jest to Node.js i npm) **pobiera nasze repo** (za pomocą `git clone`) i czyta plik .travis.yaml

Poniżej przykład takiego pliku z mojego projektu:

```
language: node_js
node_js:
  - 10.12.0
install:
  - npm install
script:
  - make
  - make lint
  - make tscheck || travis_terminate 1
  - make skipped_tests || travis_terminate 1
  - make test || travis_terminate 1
after_script:
  - make coveralls
```

W powyższym przykładzie mamy `npm install`, który doda wszystkie potrzebne zależności i po kolei nasze
polecenia.  Domyślnie Travis nie zatrzymuje działania kolejki poleceń, gdy któreś się nie powiedzie
(zwróci kod różny od 0), dlatego ja dodaje `|| travis_terminate 1`. Gdy mamy taki kod jeśli `tscheck` się nie
powiedzie, nie odpalą się testy.  Jeśli w twoim przypadku, chcesz odpalić wszystko, możesz ten kod usunąć.

**Zamiast `make`** możesz użyć np. skryptów **npm** (czyli tych komend w sekcji `scripts` pliku package.json)
i wywoływać np. `npm run lint`.  Możesz także wstawiać polecenia bezpośrednio np. odwołujące się do jakiegoś
pakietu z katalogu node_modules, lub użyć [**Grunta**](https://gruntjs.com/) lub
[**Gulpa**](https://gulpjs.com/).

Travis nie jest jedyną usługą ciągłej integracji, jest też np. [**CircleCI**](https://circleci.com), który też
ma **opcje darmową**, ale **Travis** (w domenie org) jest specjalnie **przeznaczony do projektów Open Source.**


Ważną cechą ciągłej Integracji jest to, że przy każdym commit-cie i pull request-cie będziemy
wiedzieć czy nasz projekt się zbudował i czy testy wykonały się pomyślnie. Będziemy np. wiedzieć czy możemy
zmergować czyjś PR (lub swój), i czy ostanie zmiany nie wymagają poprawek. Travis wyśle nam także email, gdy
ostanie zmiany spowodują "wysypanie" się testów oraz gdy zostaną naprawione lub nadal się wywalają.

### Jak dodać Travisa do projektu?

Dodanie do projektu jest proste, oto kolejne kroki (nie dodawałem zdjęć, ponieważ nie mogę już założyć konta,
bo już mam, a nie chciałem tworzyć fikcyjnego ani nie chciałem usuwać aplikacji Travisa bo stracił bym
historię projektu):

1. Wejdź na stronę [travis-ci.org](https://travis-ci.org)
2. Zaloguje się przez GitHuba (jedno kliknięcie i zatwierdzenie na stronie GitHuba)
3. Na stronie głównej dodaj projekt (obok `My repositories` po lewej stronie jest taki plus, po kliknięciu
   mamy listę naszych repozytoriów, które musimy odhaczyć takim ostylowanym checkboxem).
4. Kolejnym krokiem jest dodanie pliku .travis.yaml

I to wszystko, przy następnym commit'cie odpali się nasz plik, nasz projekt się zbuduje i zostanie
przetestowany.  Oczywiście musimy mieć napisane testy jednostkowe (najlepiej za pomocą jest) i jakiś system
budowania projektu, aby było co sprawdzać.

> **UWAGA:** Zawsze przed zalogowaniem sprawdzaj, na jakiej stronie jesteś **.com** czy **.org**, ponieważ jeśli
> wejdziesz na dokumentacje projektu i klikniesz logo, aby przejść na stronę główną, przejdziesz na **.com** a
> nie na **.org**, dokumentacja jest na stronie **.com**. Jeśli raz się logowałeś, nie powinno cię wylogować,
> więc jeśli nie jesteś zalogowany, to musisz sprawdzić czy jesteś na właściwej stronie.

### Co to jest Coveralls?

Coveralls to jeszcze jedna usługa. **Śledzi ona zmiany pokrycia testami**. Ciekawą funkcją Travisa i Coveralls
jest to, że wyświetlają się przy każdym **Pull Request-cie** dlatego będziemy wiedzieć, że **nie należy
merge'ować PR**, gdy projekt się nie zbuduje lub gdy procent pokrycia testami zmniejszy się za dużo.  (często
się zdarza, że zmniejsza się o 0.01% a i tak jest czerwona lampka).

### Jak dodać Coveralls do projektu?

Dodawanie do projektu jest bardzo proste. Wystarczy się **zarejestrować na stronie**
[coveralls.io](https://coveralls.io/), można się **zalogować przez GitHuba** jak i **GitLab** oraz
**BitBucket**. Po zalogowaniu i zatwierdzeniu dostępu, po lewej stronie mamy taki plusik (a jak się najedzie
na menu to pojawia się etykieta add repos) i tak jak na Travisie ostylowane checkboxy, którymi **włączamy
usługę dla danego projektu.**

Teraz **wystarczy** w naszym repo, z poziomu pliku `.travis.yaml` **odpalić testy i wygenerować specjalne
pliki**, które zawierają potrzebne **dane dla coveralls** (jeśli używasz `jest` to możesz użyć opcji
`--coverage`). Potem wystarczy odpalić:

```
./node_modules/coveralls/bin/coveralls.js < ./coverage/lcov.info
```

albo

```
cat ./coverage/lcov.info | ./node_modules/coveralls/bin/coveralls.js
```

Oczywiście aby użyć skryptu coveralls, musimy najpierw zainstalować pakiet z npm:

```
npm install coveralls --save-dev
```

Lub użyć yarn

```
yarn add coveralls --dev
```

Ważne jest także, aby wywoływać `npm install` lub `yarn install` z poziomy Travisa, inaczej nie będzie katalogu
node_modules i polecenia zwrócą błąd.

### Podsumowanie

I to by było na tyle. Jeśli wykonałeś te kroki, to powinieneś móc teraz **dodać odznaki** (ang. badges) do
pliku README, które będą wskazywać, czy **projekt się zbudował pomyślenie** i ile ma **procent pokrycia
testami**. Takie repozytoria są o wiele bardziej wiarygodne (oczywiście moim zdaniem).  Szczególnie gdy mają
duży procent pokrycia. Muszę jeszcze dodać, że duży procent pokrycia, to nie tylko wskaźnik, ale potrafi pomóc
każdemu projektowi Open Source (także projektom komercyjnym) dzięki niemu, gdy wykonujemy zmiany, mamy większą
pewność, że nic nie zepsuliśmy.  A gdy testy się wysypią to wiemy, że w najczęstszym przypadku musimy poprawić
kod, chyba że to zmiana, która modyfikuje API (wtedy musimy poprawić testy).

Aby dodać odznaki do README dodajemy taki kod (README musi być z rozszerzeniem .md lub .markdown)

dla Travisa:

```
[![travis](https://travis-ci.org/<USER>/<REPO>.svg?branch=<BRANCH>)](https://travis-ci.org/<USER>/<REPO>)
```

oraz Coveralls:

```
[![Coverage Status](https://coveralls.io/repos/github/<USER>/<REPO>/badge.svg?branch=<BRANCH>)](https://coveralls.io/github/<USER>/<REPO>?branch=BRANCH>)
```

Wyglądają one tak:

[![travis](https://travis-ci.org/jcubic/jquery.terminal.svg?branch=master)](https://travis-ci.org/jcubic/jquery.terminal)
[![Coverage Status](https://coveralls.io/repos/github/jcubic/jquery.terminal/badge.svg?branch=master)](https://coveralls.io/github/jcubic/jquery.terminal?branch=master)
