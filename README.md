# Głównie JavaScript

Źródła strony korzystające z generatora stron statycznych [jekyll](http://jekyllrb.com/)
oraz [tidy-html5](https://github.com/htacg/tidy-html5) ponieważ
[jekyll nie wypluwa kodu z poprawnymi wcięciami](https://github.com/jekyll/jekyll/issues/2640)

Pod GNU/Linuxem Ubuntu lub dystrybucjami pochodnymi wykonaj:

```
sudo apt-get install ruby ruby-dev python
sudo gem install jekyll jekyll-paginate pygments.rb bundler
pip install --user  https://github.com/jcubic/pygments-lexer-babylon/zipball/master
cd jcubic.pl
make install
```

dla dystrybucji fedora

```
sudo dnf install ruby ruby-devel python gcc gcc-c++
```

Plus komendy gem, pip oraz make install

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

Musisz mieć też zainstalowany Node.js pod komendą `nodejs` jeśli masz zainstalowany pod node to musisz wykonać
link symboliczny:

```
test -x /usr/bin/nodejs || sudo ln -s /usr/bin/node /usr/bin/nodejs
```


Aby zbudować stronę po zainstalowania programu jekyll oraz tidy-html5 wywołaj

```
make
```



wynikowa strona znajdzie się w katalogu `_site`.


Po dodaniu nowego wpisu należy dodać go do indeksu, który służy do wyszukiwania. Należy wykonać:

```
make index
```

## Docker

W repozytorium znaduje się plik Dockerfile, dzięki któremu możesz zbudować obraz dockerowy z wszystkimi
potrzebnymi zależnościami. Aby zbydować obraz wykonaj:

```
docker build -t jcubic.pl .
```

aby uruchomić kontener:

```
docker run --rm -ti -v $(pwd):/tmp/www -e "JEKYLL_ENV=docker" -p 127.0.0.1:8080:4000 jcubic.pl
```

W przeglądarce pod adress http://localhost:8080 będzie odpalony blog, który zostanie przebudownay
przy każdej zmianie pliku lub dodaniu artykułu.

Copyright (C) 2014-2018 [Jakub Jankiewicz](http://jcubic.pl/jakub-jankiewicz)

Wszystko na licencji [CC-BY-SA](http://creativecommons.org/licenses/by-sa/4.0/), chyba że napisano inaczej
