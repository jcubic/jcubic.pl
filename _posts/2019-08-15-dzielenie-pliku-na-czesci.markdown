---
layout: post
title:  "Dzielenie i Upload Plików na Części"
date:   2019-08-15 13:39:56+0200
categories:
tags: javascript API upload php
description: Wpis o tym jak użyć nowych API, aby wgrać pliki i katalogi na serwer. Upload plików i katalogów jest teraz możliwy poprzez Drag & Drop.
author: jcubic
image:
  url: "/img/knife-cut.jpg"
  width: 800
  height: 450
  alt: "Nóż który tnie warzywa"
  attribution: źródło [pixabay.com](https://pixabay.com/pl/photos/n%C3%B3%C5%BC-ci%C4%99cie-kawa%C5%82ek-kuchnia-warzyw-3923873/), licencja [Simplified Pixabay License](https://pixabay.com/pl/service/license/)
related:
  -
    name: "Upload Katalogów i Plików poprzez Drag & Drop"
    url: /2019/06/upload-katalogow-javascript.html
---

Trochę czasu minęło od ostatniego wpisu, ale spowodowane był to tym, że wakacje i urlopy. Ale
przejdźmy do rzeczy, tak jak obiecałem, w tym wpisie przedstawię, jak podzielić plik na części, aby
ominąć limit danych (np. ten w PHP).


<!-- more -->

Dzielenie pliku na części nie jest tak skomplikowane jak
[upload katalogów](2019/06/upload-katalogow-javascript.html). Mamy jedną funkcje a właściwie metodę o nazwie
`slice`, która wycina pewną część pliku. Metoda `slice` ma swoje prefiksy w różnych przeglądarkach. Należy ona
do interface'u [Blob](https://developer.mozilla.org/en-US/docs/Web/API/Blob), po którym dziedziczy obiekt File
(więcej o funkcji `slice` na [MDN](https://developer.mozilla.org/en-US/docs/Web/API/Blob/slice)). Wywołanie
metody zwraca nam obiekt typu Blob, który można z kolei dodać do obiektu
[FormData](https://developer.mozilla.org/en-US/docs/Web/API/FormData), aby wysłać na serwer.

Kod, do dzielenia pliku na części oraz wgrywania ich na serwer, wygląda tak:

{% highlight javascript %}
function upload_by_chunks(file, options) {
    var settings = Object.assign({}, {
       chunk_size: 1048576 // 1MB
    }, options);
    var chunk_size = settings.chunk_size;
    function slice(start, end) { // 1
        if (file.slice) {
            return file.slice(start, end);
        } else if (file.webkitSlice) {
            return file.webkitSlice(start, end);
        } else if (file.mozSlice) {
            return file.mozSlice(start, end);
        }
    }
    return new Promise(function(resolve, reject) {
        function process(start, end) {
            if (start < file.size) {
                var chunk = slice(start, end); // 2
                var formData = new FormData(); // 3
                formData.append('file', chunk, file.name); // 4
                formData.append('path', path);
                fetch('lib/upload.php', { // 5
                    body: formData,
                    method: 'POST'
                }).then(function(response) {
                    if (response.error) {
                        throw new Error(response.error); // 6
                    }
                    process(end, end + chunk_size); // 7
                }).catch(function(e) {
                    reject(e); // 8
                });
            } else {
                resolve();
            }
        }
        process(0, chunk_size) // 9
    });
}
{% endhighlight %}

Najpierw definiujemy funkcję pomocniczą, która obsłuży prefiksy przeglądarek (1). W głównym kodzie zwracamy
obietnicę, w której mamy funkcję - wywołujemy ją pierwszy raz (9) z wartościami początkowymi. Już wewnątrz
funkcji, wycinamy cześć, którą wyślemy na serwer (2).  Tworzymy obiekt `FormData` (3) - w taki sposób wysyła
się pliki na serwer za pomocą AJAX-a. Następnie dodajemy naszą cześć (4).  I wysyłamy na serwer za pomocą
funkcji [fetch](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API), która zwraca obietnicę. Jeśli się
powiedzie (nie będzię błędu HTTP) sprawdzamy, czy nie występuje jakiś inny błąd, który został wysłany z serwera
(zakładamy że serwer zwraca obiekt JSON). Jeśli wartość pola `error` nie będzie wartością typu `false` (może
być np. `null` lub `undefined`) to zwrócony zostanie wyjątek (6), który zostanie obsłużony przez metodę `catch`
obietnicy i zostanie odrzucona główna obietnica (8).  W przypadku, gdy wszystko jest ok, zostanie wywołana
rekurencyjnie funkcja `process` z nowymi wartościami wskazującymi na nowy kawałek pliku (7).

Aby taki kod zadziałał serwer musi być dodatkowo przygotowany. Skrypt `upload.php` musi dodawać każdą następną
cześć na koniec pliku. Musi też być obsłużone nadpisywanie plików, np. gdy wgrywasz plik, który już istnieje
na serwerze. Wtedy musisz usunąć poprzedni plik, aby nie było w nim dwóch plików jeden za drugim.

W PHP można to osiągnąć, pisząc kod, który usunie plik przed zapisywaniem kolejnych części. (można np. dodać
dodatkowy parametr dla pierwszej części pliku). Najpierw jednak dodajmy parametry w JavaScript.


{% highlight javascript %}
function upload_by_chunks(file, path, options) {
    var settings = Object.assign({}, {
        chunk_size: 1048576 // 1MB
    }, options);
    var chunk_size = settings.chunk_size;
    function slice(start, end) {
        if (file.slice) {
            return file.slice(start, end);
        } else if (file.webkitSlice) {
            return file.webkitSlice(start, end);
        } else if (file.mozSlice) {
            return file.mozSlice(start, end);
        }
    }
    return new Promise(function(resolve, reject) {
        function process(start, end, options) {
            var settings = Object.assign({}, { // 1
                chunk: true
            }, options);
            if (start < file.size) {
                var chunk = slice(start, end);
                var formData = new FormData();
                formData.append('file', chunk, file.name);
                formData.append('path', path);
                Object.keys(settings).forEach(function(key) {  // 2
                    formData.append(key, settings[key]);
                });
                fetch('upload.php', {
                    body: formData,
                    method: 'POST'
                }).then(function(response) {
                    if (response.error) {
                        throw new Error(response.error);
                    }
                    process(end, end + chunk_size);
                }).catch(function(e) {
                    reject(e);
                });
            } else {
                resolve();
            }
        }
        process(0, chunk_size, {
            first: true // 3
        });
    });
}
{% endhighlight %}

Dodaliśmy kilka dodatkowych linijek kodu. W (1) tworzymy obiekt ustawień funkcji, który zawiera domyślną
wartość `chunk: true`.  W (2) dodajemy wszystkie opcje, te przekazane jako argument oraz domyślny `chunk:
true`, do obiektu `FormData`. W (3) przekazujemy dodatkową opcje `first: true`, która zostanie przesłana do
serwera dzięki obiektowi `FormData` (2). Będzie ona określała pierwszą część pliku.

Z takim kodem w JavaScript wystarczy że napiszemy nasz plik `upload.php`:

{% highlight php startinline=true %}
// ----------------------------------------------------------------------------
// :: function that check if it's safe to use function on directory
// ----------------------------------------------------------------------------
function safe_dir($path) {
    $basedir = ini_get('open_basedir');
    if ($basedir == "") {
        return true;
    }
    foreach (explode(":", $basedir) as $safe) {
        if (preg_match("%^$safe%", $path)) {
            return true;
        }
    }
    return false;
}


header('Content-type: application/json');

try {
    if (!isset($_POST['path'])) {
        throw new Exception('Wrong request');
    }
    if (!isset($_FILES['file'])) {
        throw new Exception('No File');
    }
    if (preg_match("/\.\./", $_POST['path'])) {
        throw new Exception('No directory traversal');
    }
    $fname = basename($_FILES['file']['name']);
    $path = $_POST['path'] == '.' ? getcwd() : $_POST['path'];
    $full_name = $path . '/' . $fname;
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('File not sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception('Exceeded filesize limit.');
        default:
            throw new Exception('Unknown error.');
        }
    } elseif ((file_exists($full_name) && !is_writable($full_name)) ||
              !safe_dir($_POST['path'])) {
        throw new Exception('File "'. $fname . '" is not writable');
    } else {
         if (file_exists($full_name) && isset($_POST['first']) &&
             is_writable($full_name) && safe_dir($_POST['path'])) {
             unlink($full_name); // 1
         }
         if (isset($_POST['chunk'])) {
             $contents = file_get_contents($_FILES['file']['tmp_name']); // 2
             $file = fopen($full_name, 'a+'); // 3
             if (!$file) {
                 throw new Exception('Can\'t save file.');
             }
             if (fwrite($file, $contents) != strlen($contents)) { // 4
                 throw new Exception('Not all bytes saved.');
             }
             echo json_encode(array('success' => true));
             fclose($file);
         } else {
             if (!move_uploaded_file($_FILES['file']['tmp_name'], // 5
                                     $full_name)) {
                 throw new Exception('Can\'t save file.');
             }
             echo json_encode(array('success' => true));
         }
    }
} catch(Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}
{% endhighlight %}

Natomiast w kodzie PHP, po sprawdzeniu początkowych wyjątków, sprawdzamy czy mamy zdefiniowany zmienną POST o
nazwie `first`.  Jeśli tak to znaczy że musimy usunąć stary plik, ale tylko jeśli już istnieje (1). Następnie
pobieramy zawartość pliku (2), jeśli jest to część pliku (chunk), to otwieramy plik docelowy (3) i zapisujemy
zawartość tego co było przesłane (4), jeśli ilość bajtów się nie zgadza zwracany jest wyjątek. Jeśli nie jest
to część pliku tylko cały plik, używamy funkcji `move_uploaded_file`. Jeśli zapis się powiedzie zwracamy JSON-a
z powodzeniem zapisu.

Dzięki takiemu plikowi mamy możliwość zwykłego uploadu oraz uploadu na części.

I to by było na tyle. Do tego kodu można by jeszcze dodać pasek postępu. Wystarczy obliczyć ile części
zostanie wygenerowanych i procentowo zwiększać po wgraniu każdej z nich. Ale to zostawię jako ćwiczenie dla
czytelnika.

Dodatkowo w kodzie PHP można jeszcze, tworzyć plik tymczasowy z częściami i tylko gdy całość zostanie
poprawnie zapisana, nadpisywać oryginalny plik. Dzięki temu nie zostaniemy z niepełnym plikiem w docelowym
miejscu. Plik tymczasowy natomiast, można usunąć przy pierwszym niepoprawnym zapisie.
