# Głównie JavaScript

Źródła strony korzystające z generatora stron statycznych [jekyll](http://jekyllrb.com/)
oraz [tidy-html5](https://github.com/htacg/tidy-html5) poniewarz
[jekyll nie wypluwa kodu z poprawnymi wcięciami](https://github.com/jekyll/jekyll/issues/2640)

Pod GNU/Linuxem Ubuntu lub dystrybucjami pochodnymi wykonaj:

```
sudo apt-get install ruby ruby-dev python
sudo gem install jekyll jekyll-paginate pygments.rb
sudo pip install pygments-lexer-babylon
```

to install tidy html5 you need to use execute this:

```
sudo apt-get install cmake xsltproc

git clone https://github.com/htacg/tidy-html5
cd tidy-html5/build/cmake
cmake ../.. -DCMAKE_BUILD_TYPE=Release
make
sudo make install
```


Aby zbudować stronę po zainstalowania programu jekyll oraz tidy-html5 wywołaj

```
$ make
```

wynikowa strona znajdzie się w katalogu _site.

Copyright (C) 2014-2017 [Jakub T Jankiewicz](http://jcubic.pl/jakub-jankiewicz)

Wszystko na licencji [CC-BY-SA](http://creativecommons.org/licenses/by-sa/4.0/), chyba że napisano inaczej
