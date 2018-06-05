# Głównie JavaScript

Źródła strony korzystające z generatora stron statycznych [jekyll](http://jekyllrb.com/)
oraz [tidy-html5](https://github.com/htacg/tidy-html5) poniewarz
[jekyll nie wypluwa kodu z poprawnymi wcięciami](https://github.com/jekyll/jekyll/issues/2640)

Pod GNU/Linuxem Ubuntu lub dystrybucjami pochodnymi wykonaj:

```
sudo apt-get install ruby ruby-dev python
sudo gem install jekyll jekyll-paginate pygments.rb bundler
sudo pip install pygments-lexer-babylon
cd jcubic.pl
make install
```

dla dystrybucji fedora

```
sudo dnf install ruby ruby-devel python gcc gcc-c++
```
Plus komendy gem, pip oraz make install

aby zaisntalować tidy html5 musisz zainstalować cmake i xsltproc:

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


Aby zbudować stronę po zainstalowania programu jekyll oraz tidy-html5 wywołaj

```
make
```

wynikowa strona znajdzie się w katalogu _site.

Copyright (C) 2014-2018 [Jakub Jankiewicz](http://jcubic.pl/jakub-jankiewicz)

Wszystko na licencji [CC-BY-SA](http://creativecommons.org/licenses/by-sa/4.0/), chyba że napisano inaczej
