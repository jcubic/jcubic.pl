---
layout: post
title:  "Prosty serwer www w Pythonie"
date:   2018-02-04 19:06:32+0100
categories:
tags:  python www
author: jcubic
description: Prosty serwer www, stworzony przy pomocy gniazd (ang. sockets) w Pythonie.
image:
  url: "/img/python.jpg"
  alt: "Zdjęcie pytona"
---

Python posiada wbudowany serwer www, który można uruchomić za pomocą polecenia
`python -m SimpleHTTPServer 8000`, który serwuje pliki z aktualnego katalogu.
W tym artykule natomiast, przestawię jak napisać prosty serwer HTTP za pomocą gniazd
(ang. sockets).


<!-- more -->

Pierwszą rzeczą, jest zaimportowanie potrzebnych modułów:

{% highlight python %}
import socket
import re
import os
import threading
{% endhighlight %}

Nasz główny program powinien otworzyć gniazdo, i nasłuchiwać na wybranym porcie, następnie
powinien utworzyć wątek dla każdego połączenia, który obsłuży tego klienta.

{% highlight python %}
if __name__ == '__main__':
    try:
        server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        # jeśli proces został zakończony ale nie zamknięto gniazda
        # poniższe wywołanie odzyska gniazdo
        server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        server.bind(("0.0.0.0", 8080))
        server.listen(5)
        # główna pętla serwera
        while True:
            # dla każdego przychodzącego połączenia wywołanie funkcji w wątku
            client, addr = server.accept()
            client_handler = threading.Thread(target = handler, args=(client,))
            client_handler.start()
    except KeyboardInterrupt:
        # w przypadku gdy ktoś naciśnie CTRL+C musimy zamknąć gniazdo
        server.close()
{% endhighlight %}

Jeśli z jakiegoś powodu nie zamkniecie połączenia i zabijecie proces Pythona, `bind`
wyrzuci wyjątek `socket.error`, aby temu zaradzić będziecie musieli "odzyskać" gniazdo,
za pomocą tej linijki:

{% highlight python %}
server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
{% endhighlight %}

Ale powyższy skrypt obsługuje zabicie procesu za pomocą `CTRL+C` dlatego nie powinno się
to wydarzyć.

Następnym krokiem, jest napisanie głównej funkcji, która jest przekazywana jako parametr
`target` do konstruktora `threading.Thread`. Funkcja `handler` wygląda tak:

{% highlight python %}
def handler(socket):
    global header_re
    request = get_request_data(socket)
    m = re.search(header_re, request[0])
    if m:
        root = os.getcwd()
        matches = m.groups()
        # informacja o tym jaki plik jest pobierany
        print "request %s" % matches[1]
        if matches[1] == "/":
            fname = "index.html"
        else:
            fname = matches[1][1:]
        path = os.path.join(root, fname)
        # metoda HEAD zwraca same nagłówki
        if os.path.exists(path):
            if matches[0] == "HEAD":
                content = ""
            else:
                content = open(path, "r").read()
            socket.send(response(200, content, mime(fname)))
        else:
            if matches[0] == "HEAD":
                content = ""
            else:
                content = "404 Page Not found"
            socket.send(response(404, content))
    socket.close()
{% endhighlight %}

W powyższej funkcji, użyto zmiennej `header_re`, która zawiera wyrażenie regularne, które
pozwala na wyłuskanie metody HTTP oraz ścieżki do pliku:

{% highlight python %}
header_re = re.compile(r"(GET|POST) ([^ ]+) HTTP/", re.I)
{% endhighlight %}

W funkcji `handler` użyto kilku funkcji pomocniczych:

1. `get_request_data`, która czyta wszystkie dane z gniazda i zwraca listę. W naszym programie
   używamy tylko pierwszego elementu czyli nagłówków protokołu HTTP. Drugim elementem byłyby
   dane wysłane za pomocą metody POST.

{% highlight python %}
def get_request_data(socket):
    request = []
    while True:
        data = socket.recv(100)
        request.append(data)
        if len(data) < 100:
            break
    return "".join(request).split("\r\n\r\n", 1)
{% endhighlight %}

2. funkcja `status`, która zwraca status HTTP wraz z kodem, tylko dwa rodzaje 404 oraz 200
   zostały użyte.

{% highlight python %}
def status(code):
    if code == 200:
        return "200 OK"
    elif code == 404:
        return "404 Not Found"
{% endhighlight %}

3. `response` - funkcja, która zwraca odpowiedź HTTP jako ciąg znaków:

{% highlight python %}
def response(code, data, mime = "text/plain", headers = None):
    response_headers = {
        "Server": "Python",
        "Content-Type": mime,
        "Content-Length": len(data),
        "Connection": "close"
    }
    if headers:
        response_headers.update(headers)
    headers = "\r\n".join([ "%s: %s" % (k,v) for k, v in response_headers.items()])
    res = "HTTP/1.1 %s\r\n%s\r\n\r\n%s"
    return res % (status(code), headers, data)
{% endhighlight %}

4. `mime` jest ostatnią użytą funkcją, która zwraca MIME czyli typ, który jest rozpoznawany
   przez przeglądarkę, np. `text/html`. Typ MIME informuje przeglądarkę, jak wyświetlić
   odpowiedź z serwera. Nic nie stoi na przeszkodzie aby np. wyświetlić stronę z rozszerzeniem
   html jako obrazek. (jeśli nie jest to obrazek, to wyświetli się ikonką niepoprawnego obrazka)

{% highlight python %}
def mime(fname):
    ext = os.path.splitext(fname)[1]
    if ext == '.html':
        return 'text/html'
    elif ext == '.js':
        return 'application/javascript'
    elif ext == '.jpg' or ext == '.jpeg':
        return 'image/jpeg'
    elif ext == ".png":
        return 'image/png'
    elif ext == '.css':
        return 'text/css'
    else:
        return 'text/plain'
{% endhighlight %}

Zamiast funkcji można by też użyć słownika, którego kluczami byłyby rozszerzenia, natomiast
wartościami typy MIME.

Jest to przykład prostego serwera, który może być przydatny w debugowaniu, można go rozszerzyć
np. o skrypty CGI albo o obsługę plików PHP (aby dodać pliki PHP należałoby skorzystać z
polecenia PHP, ale przed wywołaniem należałoby przypisać odpowiednie zmienne środowiskowe,
dodam że nie testowałem).

Cały skrypt można znaleźć na [githubie](https://gist.github.com/jcubic/e322940752f50b6c1cec08166fd5ea4b).

Więcej informacji o protokole HTTP, możesz znaleźć w
[Wikipedii](https://pl.wikipedia.org/wiki/HTTP), natomiast pełny opis protokołu
można znaleźć w [dokumentach RFC](https://www.rfc-editor.org/search/rfc_search_detail.php?title=http&pubstatus%5B%5D=Any&pub_date_type=any) (ang. Request for Comments).
