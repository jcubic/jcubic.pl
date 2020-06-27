---
layout: post
title:  "Server WWW w przeglądarce"
date:   2018-08-27 20:25:43+0200
categories:
tags:  javascript html5 pwa api service-worker www
author: jcubic
description: Użycie Service Worker-a w celu serwowania plików statyczncych, tworzonych w przeglądarce, tak jakby znajdowały się na serwerze.
image:
  url: "/img/web.jpg"
  alt: "Duży napis WEB złożony z innych słów"
---

Nie jest to implementacja serwera www całkowicie w JavaScript oraz przeglądarce, ale serwowanie
statycznych plików, tworzonych w przeglądarce, tak jakby były zwracane przez prawdziwy serwer,
więc można, z przymrużeniem oka, nazwać go serwerem www w przeglądarce.

<!-- more -->

Maiłem okazje napisać taki kod, przy pisaniu małej aplikacji
[GIT Web Terminal](https://jcubic.github.io/git/), która korzystając z biblioteki
[isomorphic-git](https://github.com/isomorphic-git/isomorphic-git), udostępnia interface
wiersza poleceń do ograniczonego korzystania z gita. Można sklonować repozytorium z GitHuba
edytować pliki używając komend `vi` lub `emacs` (vi czasami się wykrzacza). Można także commit-ować
i push-ować zmiany z powrotem do GitHuba. Jedną z funkcji jest możliwość podglądu plików,
które są zapisywane do bazy indexedDB za pomocą biblioteki [BrowserFS](https://github.com/jvilk/BrowserFS).
Można otwierać pliki za pomocą komendy `view`, która otwiera plik w iframe-ie,
który wygląda jak przeglądarka www. Można także otwierać pliki poprzez adres url. Dodając między adresem strony,
a ścieżką do pliku, ciąg znaków `__browserfs__`. Dzięki temu istnieje np. możliwość edycji aplikacji www
kontrolowanej przez git-a, z poziomu przeglądarki i natychmiastowego podglądu w jednej zakładce przeglądarki.

Sposób funkcjionowania tego mechanizmu to
[Service Worker](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API).

Jest to narzędzie, które zostało głównie stworzone w celu cache-owania danych w przeglądarce,
aby była możliwość korzystania z aplikacji, kiedy nie ma internetu (offline).
Głównymi aplikacjami, które korzystają z tego narzędzia są PWA, o których ostatnio było bardzo głośno.

Service Worker znajduje się między przeglądarką a serwerem www, czyli działa jak proxy.
Można nasłuchiwać na zdarzenie fetch, które wywołuje się za każdym razem gdy strona wysyła zapytanie HTTP
do serwera, i nie chodzi tylko o AJAXa i funkcje fetch, ale o każde zapytanie wysyłane przez przeglądarkę.
Inną cechą Service Worker-ów, jest to że działają nawet gdy strona, która go utworzyła została zamknięta.
Service Worker zostanie w takim przypadku uśpiony, dopóki nie nastąpi ponowne zapytanie HTTP, które znajdzie
się w jego zakresie. Ważne jest także to, że Service Worker ma dostęp do bazy indexedDB czyli miejsca gdzie
przechowuje pliki w mojej aplikacji.

Poniżej kod Service Worker-a, którego użyłem w aplikacji GIT Web Terminal.

{% highlight javascript %}
self.importScripts('https://cdn.jsdelivr.net/npm/browserfs');

self.addEventListener('install', self.skipWaiting);

self.addEventListener('activate', self.skipWaiting);

self.addEventListener('fetch', function (event) {
    let path = BrowserFS.BFSRequire('path');
    let fs = new Promise(function(resolve, reject) {
        BrowserFS.configure({ fs: 'IndexedDB', options: {} }, function (err) {
            if (err) {
                reject(err);
            } else {
                resolve(BrowserFS.BFSRequire('fs'));
            }
        });
    });
    event.respondWith(fs.then(function(fs) {
        return new Promise(function(resolve, reject) {
            function sendFile(path) {
                fs.readFile(path, function(err, buffer) {
                    if (err) {
                        err.fn = 'readFile(' + path + ')';
                        return reject(err);
                    }
                    var ext = path.replace(/.*\./, '');
                    var mime = {
                        'html': 'text/html',
                        'json': 'application/json',
                        'js': 'application/javascript',
                        'css': 'text/css'
                    };
                    var headers = new Headers({
                        'Content-Type': mime[ext]
                    });
                    resolve(new Response(buffer, {headers}));
                });
            }
            var url = event.request.url;
            var m = url.match(/__browserfs__(.*)/);
            function redirect_dir() {
                return resolve(Response.redirect(url + '/', 301));
            }
            function serve() {
                fs.stat(path, function(err, stat) {
                    if (err) {
                        return resolve(textResponse(error404Page(path)));
                    }
                    if (stat.isFile()) {
                        sendFile(path);
                    } else if (stat.isDirectory()) {
                        if (path.substr(-1, 1) !== '/') {
                            return redirect_dir();
                        }
                        fs.readdir(path, function(err, list) {
                            if (err) {
                                err.fn = 'readdir(' + path + ')';
                                return reject(err);
                            }
                            var len = list.length;
                            if (list.includes('index.html')) {
                                sendFile(path + '/index.html');
                            } else {
                                listDirectory({fs, path, list}).then(function(list) {
                                    resolve(textResponse(fileListingPage(path, list)));
                                }).catch(reject);
                            }
                        });
                    }
                });
            }
            if (m) {
                var path = m[1];
                if (path === '') {
                    return redirect_dir();
                }
                console.log('serving ' + path + ' from browserfs');
                serve();
            } else {
                if (event.request.cache === 'only-if-cached' && event.request.mode !== 'same-origin') {
                    return;
                }
                //request = credentials: 'include'
                fetch(event.request).then(resolve).catch(reject);
            }
        });
    }));
});
// -----------------------------------------------------------------------------
function listDirectory({fs, path, list}) {
    return new Promise(function(resolve, reject) {
        var items = [];
        (function loop() {
            var item = list.shift();
            if (!item) {
                return resolve(items);
            }
            fs.stat(path + '/' + item, function(err, stat) {
                if (err) {
                    err.fn = 'stat(' + path + '/' + item + ')';
                    return reject(err);
                }
                items.push(stat.isDirectory() ? item + '/' : item);
                loop();
            });
        })();
    });
}

// -----------------------------------------------------------------------------
function textResponse(string, filename) {
    var blob = new Blob([string], {
        type: 'text/html'
    });
    return new Response(blob);
}

// -----------------------------------------------------------------------------
function fileListingPage(path, list) {
    var output = [
        '<!DOCTYPE html>',
        '<html>',
        '<body>',
        `<h1>BrowserFS ${path}</h1>`,
        '<ul>'
    ];
    if (path.match(/^\/(.*\/)/)) {
        output.push('<li><a href="..">..</a></li>');
    }
    list.forEach(function(name) {
        output.push('<li><a href="' + name + '">' + name + '</a></li>');
    });
    output = output.concat(['</ul>', '</body>', '</html>']);
    return output.join('\n');
}

// -----------------------------------------------------------------------------
function error404Page(path) {
    var output = [
        '<!DOCTYPE html>',
        '<html>',
        '<body>',
        '<h1>404 File Not Found</h1>',
        `<p>File ${path} not found in browserfs`,
        '</body>',
        '</html>'
    ];
    return output.join('\n');
}
{% endhighlight %}

Service Worker oprócz plików, zwraca także listing plików dla katalogu oraz zwraca stronę 404,
w przypadku nie znalezienia pliku, w przeglądarkowym systemie plików.

Aby uruchomić ten Service Worker wystarczy poniższy kod:

{% highlight javascript %}
if ('serviceWorker' in navigator) {
    var scope = location.pathname.replace(/\/[^\/]+$/, '/');
    if (!scope.match(/__browserfs__/)) {
        navigator.serviceWorker.register('sw.js', {scope})
                 .then(function(reg) {
                     reg.addEventListener('updatefound', function() {
                         var installingWorker = reg.installing;
                         console.log('A new service worker is being installed:',
                                     installingWorker);
                     });
                     // registration worked
                     console.log('Registration succeeded. Scope is ' + reg.scope);
                 }).catch(function(error) {
                     // registration failed
                     console.log('Registration failed with ' + error);
                 });
    }
}
{% endhighlight %}

Kod zabezpiecza się przed przypadkiem gdy aplikacje (samą siebie) odpala się poprzez browserfs czyli
gdy sklonujemy ją do katalogu /git i odpalamy z adresu https://jcubic.github.io/git/__browserfs__/git/.

Ważną rzeczą w przypadku Service Worker-a jest to, aby był umieszczony w katalogu główny aplikacji.
Ponieważ ma on możliwość przechwytywania zapytań HTTP, tylko dla adresów, które znajdują się
w katalogu, w którym został umieszczony plik Service Worker-a lub w jednym z podkatalogów.

Jedno z ograniczeń Service Worker-a jest to, że można go odpalić tylko z prawdziwego pliku, nie można odpalać go
z pliku, który sam jest zwracany przez innego Service Worker-a. Dlatego poprzez GIT Web Terminal, nie będzie można
uruchomić aplikacji, która sama korzysta z Service Worker-a.

*[PR]: Pull Request
*[PWA]: Progressive Web Apps
