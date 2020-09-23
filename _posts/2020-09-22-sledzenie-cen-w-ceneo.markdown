---
layout: post
title:  "Śledzenie cen na Ceneo.pl w Pythonie"
date:   2020-09-22 08:51:39+0200
categories:
tags: python web-scraping
author: jcubic
description: Jak śledzić ceny ofert sklepów na dowolne produkty w Ceneo zmieniające się w czasie.
image:
 url: "/img/price.png"
 alt: "Grafika wektorowa przedstawiająca wykres oraz logo Ceneo.pl"
 width: 800
 height: 562
 attribution: Jakub T. Jankiewicz, licencja [CC-BY-SA](https://creativecommons.org/licenses/by-sa/4.0/). źródło na [GitHub-ie](https://github.com/jcubic/jcubic.pl/blob/master/img/price.svg) bazuje na [logo Ceneo](https://commons.wikimedia.org/wiki/File:Ceneo_na_wiki.svg) licencja Public Domain.
---

W tym roku wyszedł nowy aparat lustrzanka nikona d780. Zastanawiałem się nad zakupem tego aparatu.
Ze względu na to, że to nowy aparat, jego cena jest dość duża i jeszcze spadnie.
Dlatego postanowiłem napisać sobie skrypt, który będzie śledził mi cenę tego produktu
na Ceneo.

W tym wpisie przedstawię jak napisać program w Pythonie, który będzie pobierał ceny z Ceneo
dowolnego produktu i zapisywał je w bazie danych SQLite.

<!-- more -->

TL;DR: kod programu dostępny na [GitHubie](https://github.com/jcubic/price.py).

Pierwszą rzeczą jaką zrobiłem, zanim zacząłem pisać kod, było sprawdzenie czy coś takiego nie jest
dostępne online. Znalazłem tylko stronę [Skąpiec](https://www.skapiec.pl/), która pokazuje wykres,
ale jest to najniża cena. Nie można także porównać cen w różnych sklepach, jak zmieniają się w czasie.

## Web Scraper

Aby móc pobrać dane o cenach mojego produktu (czyli aparatu Nikon d780) musiałem napisać
tzw. [scraper](https://en.wikipedia.org/wiki/Web_scraping), czyli program, który pobiera stronę www
(dokładnie kod html) i zczytuje z niej dane.

W pythonie do tego celu używa się paczki o nazwie
[BeautifulSoup](https://pypi.org/project/beautifulsoup4/).

Aby ją zainstalować wystarczy użyć narzędzia PIP.

{% highlight bash %}
pip install bs4
{% endhighlight %}

Pisząc scraper warto pobrać sobie stronę html na dysk, aby przez przypadek nie paść ofiarą blokady IP,
która może być zainstalowana na serwerze. Pisząc nasz skrypt, będziemy przecież wiele razy pobierać
tą samą stronę. Warto się zabezpieczyć. Moja strona ma url: `https://www.ceneo.pl/90254370`.
Zapiszmy ją na dysk jako `page.html`.

Aby wyciągnąć informacje o sklepie i cenie oraz formę dostawy, najlepiej jest użyć inspektora w
narzędziu dev tools. Poniewarz pokazuje nam strukturę html, ale zawsze trzeba potwierdzić czy to
samo znajduje się w kodzie html (strona może być dynamiczna i zmieniać strukturę dokumentu).

Na stronie Ceneo (przynajmniej w moim przypadku) są dwie tabele:

"Najlepsze oferty wybrane przez Ceneo" oraz "Nikon D780 Body Czarny - Pozostałe oferty"

Wystarczy kliknąć prawym klawiszem na ikonkę z nawą firmy i wybrać inspect (zbadaj) z menu kontekstowego.

Zauważyć można że jest tam obrazek img, ale jego alt to nazwa sklepu więc będzie to pierwszy element
do pobrania.

{% highlight html %}
<img class="partner-badge" data-hint="Autoryzowany partner Nikon"
     data-offset-y="45" data-offset-x="35"
     src="//image.ceneostatic.pl/data/manufacturer/123/manufacturerSmall.jpg"
     alt="Partner Nikon">
{% endhighlight %}

Cena produktu to element o klasie `.price`, który ma dwa elementy `span` ale wystarczy pobrać jego
tekst i uzyskamy całą cenę.  Zamieniając znak przecinka na kropkę uzyskamy wartość float (w
zależności od przedmiotu mogą się pojawić grosze).

{% highlight html %}
<span class="price">
  <span class="value">8989</span><span class="penny">,00</span>
</span>
{% endhighlight %}

Oprócz ceny i sklepu warto pobrać też meta dane jak już tu jesteśmy.

Możemy pobrać liczbę gwiazdek sklepu oraz liczbę opinii. Będziemy mogli sobie potem odpowiednio
posortować dane.

{% highlight html %}
<td class="cell-store-review">
  <img alt="Zaufane opinie" data-original="/content/img/icons/trusted.png"
       class="js_trustedReviewsTooltip lazyloaded" data-offset-y="36"
       data-offset-x="-12" src="/content/img/icons/trusted.png">
  <span class="stars js_mini-shop-info js_no-conv" data-mini-shop-info-url="Partials/ShopMiniInfo?shopId=27143">
    <span class="score-container score-container--s  js_score-container">
      <span class="score-marker score-marker--s" style="width: 100%;"></span>
    </span>
    <span class="screen-reader-text">Ocena 5 / 5</span>
  </span>
  <span class="link link--accent js_mini-shop-info js_no-conv"
        data-mini-shop-info-url="Partials/ShopMiniInfo?shopId=27143">792 opinie</span>
</td>
{% endhighlight %}

Nie trzeba wyciągać informacji o gwiazdkach, ktoś pomyślał o dostępności dla czytników dla
niewidomych i zapisana jest informacja o ocenie liczbowej.


Warto tez pobrać wartość wysyłki i dodać do ceny produktu. Można także zapisać wysyłkę w osobnym polu,
Jeśli będzie darmowa wstawimy 0.

{% highlight html %}
<span class="free-delivery-txt" data-offset-x="12">Darmowa wysyłka</span>
{% endhighlight %}

Można też dodać flagę "dostępny"

{% highlight html %}
<span class="instock">dostępny</span>
{% endhighlight %}

Poniżej cena wraz z wysyłką (nie formatowałem, aby pokazać białe znaki).

{% highlight html %}
<div class="product-delivery-info js_deliveryInfo" data-info-hook="dJKJChs5zbnw2tDXhaBLQj9WytJtPAHK5v-EAMcSK6fz3TIniOWitx80zBe6vQHj1LSagyUVU7vB4hGOY4DPYKNHtcx4kDmZUBGuubOrHKiNyhLJrPG-iYtTj3bWANyHpVBMwlkFDd2lUEzCWQUN3dR_PgKROHW8PmhGx7LpgBelUEzCWQUN3aVQTMJZBQ3dLKIQDlE7GCVDRZzezlhe0JY7NXUM9WKVSfAn2aBty9lh6UDz94e6ATiFsxB0P1q2JBJnOpPuSEA_x-GY2zB2bQ==&amp;a=2" data-productid="90254370" data-offset-x="-10">
                                 Z wysyłką od
9019,00                                zł
                </div>
{% endhighlight %}

Poniżej funkcja, która pobierze wszystkie te informacje z html.

{% highlight python %}
from bs4 import BeautifulSoup

def parse(html):
    """Function parse html and extract list of prices from ceneo.pl website."""
    result = []
    soup = BeautifulSoup(html, 'html.parser')
    tables = soup.find_all('table', class_ = "product-offers")
    for table in tables:
        rows = table.find_all('tr', class_ = 'product-offer')
        for row in rows:
            item = {}
            node = row.find('span', class_="price-format")
            if node is None:
                raise Exception("Error: Wrong price html node")
            item['price'] = real_price(node.text.strip())
            node = row.find(class_ = "stars")
            item['score'] = real_score(node.text.strip())
            node = row.find(class_ = 'dotted-link')
            item['opinions'] = int_opinions(node.text.strip())
            node = row.find('td', class_ = 'cell-store-logo')
            node = node.find('img')
            if node is None:
                raise Exception("Error: Image with shop log is None")
            item['shop'] = node['alt']
            if item['shop'] is None:
                raise Exception("Error: no alt on shop image")
            node = row.find(class_ = 'product-delivery-info')
            delivery = real_delivery(node.text.strip())
            if delivery > 0:
                item['delivery'] = delivery - item['price']
            else:
                item['delivery'] = 0
            node = row.find(class_ = 'product-availability')
            item['available'] = node.text.strip()
            result.append(item)
    return result
{% endhighlight %}

W funkcji użyto kilka mniejszych fukcji pomocniczych:

{% highlight python %}
def real_price(str):
    """Function return real price from the string."""
    return float(str.replace(',', '.').replace('zł', ''))

def real_score(str):
    """Function return score - number from 1 to 5"""
    return float(re.sub(r'[^0-9,/]|/\s*5', '', str).replace(',', '.'))

def int_opinions(str):
    """Function parses opionon count"""
    return int(re.sub('[^0-9]', '', str))

def real_delivery(str):
    """Function return float numer from delivery string"""
    str = re.sub('[^0-9,]', '', str)
    if len(str) == 0:
        return 0
    return float(str.replace(',', '.'))
{% endhighlight %}

## Baza SQLite

Będziemy zapisać dane do bazy SQLite, więc musimy ją zainicjować:

{% highlight python %}
def init_db():
    DB_NAME = 'price.db'
    conn = sqlite3.connect(DB_NAME)
    c = conn.cursor()
    for query in open('init.sql').read().split(';'):
        c.execute(query)
    conn.commit()
    return conn
{% endhighlight %}

Baza będzie relacyjna, oto kod SQL, który utworzy bazę danych (plik **init.sql**).

{% highlight sql %}
CREATE TABLE IF NOT EXISTS shop (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(400)
);

CREATE TABLE IF NOT EXISTS product (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(800)
);

CREATE TABLE IF NOT EXISTS price (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    shop INTEGER NOT NULL,
    product INTEGER NOT NULL,
    delivery VARCHAR(100),
    score REAL,
    opinions INTEGER,
    avaiable INTEGER,
    price REAL,
    time INTEGER NOT NULL,
    FOREIGN KEY(shop) REFERENCES shop(id),
    FOREIGN KEY(product) REFERENCES product(id),
    FOREIGN KEY(time) REFERENCES time(id)
);

CREATE TABLE IF NOT EXISTS time (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    time INTEGER
);
{% endhighlight %}

Poniżej kod główny programu, który zapisze dane do bazy.

{% highlight python %}
import sqlite3
from datetime import datetime

if __name___ === '__main__':
    conn = init_db()
    c = conn.cursor()
    time = now()
    c.execute('INSERT INTO time(time) VALUES (?)', (time,))
    conn.commit()
    time_id = c.lastrowid
    c.execute('SELECT id, name FROM shop')
    shops = c.fetchall()
    c.execute('SELECT id FROM product WHERE name like ?', (product,))
    products = c.fetchall()

    if len(products) == 0:
        c.execute('INSERT INTO product(name) VALUES(?)', (product, ))
        conn.commit()
        product_id = c.lastrowid
    else:
        product_id = products[0][0]

    for offer in parse(request(url)):
        shop = get_shop(shops, offer['shop'])
        if shop is None:
            c.execute('INSERT INTO shop(name) VALUES(?)', (offer['shop'],))
            conn.commit()
            shop_id = c.lastrowid
        else:
            shop_id = shop[0]
        data = (
            shop_id, product_id, offer['score'], offer['opinions'],
            offer['available'], offer['price'], offer['delivery'],
            time_id
        )
        c.execute('''INSERT INTO price (shop, product, score, opinions, avaiable, price, delivery, time)
                     VALUES(?,?,?,?,?,?,?,?)''', data)

        print("price: %s from %s (%s)" % (offer['price'], offer['shop'], shop_id))
    conn.commit()
{% endhighlight %}

Oraz użyte funkcje pomocnicze poniżej:

{% highlight python %}
def now():
    """Function return timestamp from current datetime."""
    return int(datetime.now().timestamp())

def request(url):
    """get html from url as string."""
    req = urlopen(url)
    code = req.getcode()
    if code == 200:
        return req.read().decode('utf-8')
    else:
        raise Exception("Error Code: %s when accessing %s" % (code, url))

def find(lst, cond):
  """Function return first element from list by executing the function cond."""
  for e in lst:
      if cond(e):
          return e
  return None

def get_shop(shops, name):
    """Function return given shop info from parsed data."""
    return find(shops, lambda x: x[1] == name)
{% endhighlight %}



To co jeszcze warto dodać to obsługa wiersza poleceń, aby łatwiej wywoływać skrypt.

Po dodaniu wiersza poleceń można dodać skrypt do crona (czyli programu w systemach
unixowych, który cyklicznie odpala nasze polecenia). Cron dostępny jest
pod GNU/Linuksem, prawdopodobnie też dla Windows 10 WSL oraz MacOSX.


Aby dodać do crona, aby codziennie pobierał dane, należy dopisać wartość w crontab.
Wywołać polecenie `crontab -e` jesli nie masz ustawionego edytora prawdopodobnie otworzy
się edytor `vi`.

Oraz wpisując:
```
15 10 * * * /path/to/file/price.py
```

Program uruchomi się codziennie o 10:15. Warto dodać pełną ścieżkę do programu,
ponieważ Cron może jej nie znać. Należy także ustawić, aby można było uruchomić
skrypt (Dodać tzw. shebang `#!/usr/bin/env python` oraz ustawić `chmod a+x price.py`)

Jeśli chcemy uruchomić go dla kilku produktów, można dać odstęp kilku minut.

Jeśli odpalamy skrypt z Corna trzeba dodać jeszcze jedną rzecz, zmienić domyślny katalog.

{% highlight python %}
def chwd():
    script = os.path.realpath(__file__)
    path = os.path.dirname(script)
    os.chdir(path)
{% endhighlight %}

Inaczej skrypt nie znajdzie naszych plików w katalogu w którym się znajduje.

Cały kod wraz z logowaniem błędów znajduje się na [GitHubie](https://github.com/jcubic/price.py).
Cały kod napisany został w Pythona 3.

## Podsumowanie

Czasami gdy strona lub aplikacja internetowa nie udostępnia API, web scraping, czyli pobieranie
danych bezpośrednio ze strony, to jedyna opcja. Jak widać nie jest to wcale takie trudne zdanie.

W jednym z następnych wpisów, pokażę jak wyświetlić dane z bazy w postaci wykresu. Tym razem
użyje pewnie do tego celu jedną z bibliotek języka JavaScript.

## Aktualizacja 2020-09-23

Dopiero teraz zauważyłem, że skrypt przestał działać. Zmieniła się klasa elementu z opinią (teraz to
`link--accent`) i skrypt zaczął zwracał wyjątek. Początkowo miałem zaplanowane wysyłanie emaili, gdy
cena spadnie. Dodałem jednak wysyłanie wiadomości, gdy skrypt zwróci wyjątek, dzięki temu na
przyszłość będzie można poprawić skrypt, gdy coś się zmieni na stronie.

Dodany został taki kod (dodatkowo na GitHubie jest jeszcze obsługa opcji pocztowy z wiersza poleceń):

{% highlight python %}
import smtplib

message = """From: Me <YOUR EMAIL>
To: Me <YOUR EMAIL>
Subject: Price Error

There is error in price.py

%s
"""

def error(e, email = True):
    logger.error(e)
    exc_type, exc_value, exc_tb = sys.exc_info()
    stack = ''.join(traceback.format_exception(exc_type, exc_value, exc_tb))
    print(stack)
    try:
        if email:
            s = smtplib.SMTP('<SERVER>')
            s.login('<USER>', '<PASSWORD>')
            s.sendmail('<FROM email>', '<TO EMAIL>', message % stack)
            s.quit()
    except Exception as e:
        error(e, False)

{% endhighlight %}

W głównym kodzie programu, gdy zostanie wyrzucony wyjątek zostanie on zalogowany i wysłany email.

