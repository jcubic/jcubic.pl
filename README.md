# Głównie JavaScript

![powered by Jekyll](https://img.shields.io/badge/powered_by-Jekyll-blue.svg)

Źródła strony korzystające z generatora stron statycznych [jekyll](http://jekyllrb.com/)
oraz [tidy-html5](https://github.com/htacg/tidy-html5) ponieważ
[jekyll nie wypluwa kodu z poprawnymi wcięciami](https://github.com/jekyll/jekyll/issues/2640).

Pod GNU/Linuxem Ubuntu lub dystrybucjami pochodnymi wykonaj:

```
sudo apt-get install ruby ruby-dev python
sudo gem bundler
bundle install
## mój fork, który obsługuje nową składnie JavaScript, której czasami używam
pip install --user  https://github.com/jcubic/pygments-lexer-babylon/zipball/master
cd jcubic.pl
make install
```

dla dystrybucji fedora

```
sudo dnf install ruby ruby-devel python gcc gcc-c++
```

Plus komendy gem, bundle, pip oraz make install

aby zainstalować tidy html5 musisz zainstalować cmake i xsltproc:

```
sudo apt-get install cmake xsltproc
```

lub

```
sudo dnf install cmake libxslt
```

i potem

```
git clone https://github.com/htacg/tidy-html5
cd tidy-html5/build/cmake
cmake ../.. -DCMAKE_BUILD_TYPE=Release
make
sudo make install
```

Musisz mieć też zainstalowany Node.js pod komendą `nodejs` jeśli masz zainstalowany pod node
to musisz wykonać link symboliczny:

```
test -x /usr/bin/nodejs || sudo ln -s /usr/bin/node /usr/bin/nodejs
```

wszystko przez lexer do JavaScript-u (to taki Frankenstein).


Aby zbudować stronę po zainstalowania wszystkich zależności wykonaj:

```
make
```

(jak nie działa możesz spróbować dockera poniżej).

wynikowa strona znajdzie się w katalogu `_site`.


Po dodaniu nowego wpisu należy dodać go do indeksu, który służy do wyszukiwania. Należy wykonać:

```
make index
```

## Docker

W repozytorium znajduje się plik Dockerfile oraz skrypt bash'a `dock`, dzięki któremu
możesz zbudować obraz dockerowy z wszystkimi potrzebnymi zależnościami. Aby zbudować obraz
wykonaj (budowanie trochę trwa, więc można iść na kawę albo obiad):

```
./dock build
```

aby uruchomić kontener, trzeba wykonać polecenie (z katalogu z repozytorium, ponieważ
pliki z blogiem nie są zapisane w obrazie):

```
./dock
```

W przeglądarce pod adresem http://localhost:8080 będzie odpalony blog, który zostanie
przebudowany przy każdej zmianie pliku lub dodaniu artykułu. Można też dodać
`bash` (do poprzedniego polecenia), aby uzyskać wiersz poleceń.

**UWAGA**: wyszukiwarka nie będzie działać, ponieważ `jekyll serve` używa prostego
serwera, który nie obsługuje PHP.

Aby zbudować wersje produkcyjną strony z adresem z `_config.yml` wykonaj:

```
./dock make
```

## Licencja

Copyright (C) 2014-2019 [Jakub Jankiewicz](https://jcubic.pl/jakub-jankiewicz)

Wszystko na licencji [CC-BY-SA](https://creativecommons.org/licenses/by-sa/4.0/),
chyba że napisano inaczej
