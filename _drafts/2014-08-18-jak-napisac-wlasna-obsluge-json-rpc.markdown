---
layout: post
title:  "Jak Napisać Własną Obsługę Protokołu JSON-RPC w JavaScript i PHP"
date:   2014-08-18 21:12:07
categories: 
tags: javascript php json-rpc
author: jcubic
---

[JSON-RPC](http://pl.wikipedia.org/wiki/JSON-RPC) to protokół zdalnego wywoływania procedur (ang. Remote Procedure Call), w którym zapytanie oraz wynik zwracany przez serwer są przesyłane w postaci [JSON-a](http://pl.wikipedia.org/wiki/JSON) (skrót od JavaScript Object Notation). Jest to bardzo prosty protokół, który zazwyczaj implementuje się ponad [HTTP](http://pl.wikipedia.org/wiki/HTTP).

<!-- more -->

Przykładowe zapytanie może wyglądać tak jak poniżej:

{% highlight javascript %}
{"id": 1, "jsonrpc":"2.0", "method": "echo", "params": ["hello"]}
{% endhighlight %}

Odpowiedzią serwera może być poniższy tekst:

{% highlight javascript %}
{"id": 1, "jsonrpc":"2.0", "error": null, "result": "hello"}
{% endhighlight %}

Protokół JSON-RPC jest fantastycznym sposobem na organizację aplikacji SPA (ang. Single Page Application). Jeśli użyjemy odpowiedniej implementacji, w której każda zdalna procedura będzie miała swój odpowiednik w JavaScript-cie, będziemy mogli całkowicie zapomnieć o warstwie, która łączy klienta (czyli przeglądarkę internetową) oraz serwer. Możemy uprościć nasz model myślowy i myśleć tak, że wywołanie funkcji w JavaScript-cie wywołuje kod po stronie serwera. Mając otwarte dwa pliki, klienta oraz serwera możemy o nich myśleć jak o części tej samej aplikacji, będą się tylko różniły składnią, jeśli użyjemy innych języków.

W dalszej części artykułu przedstawię jak napisać prostą implementację protokołu JSON-RPC w PHP oraz JavaScript-cie z wykorzystaniem obiektów Deferred z biblioteki jQuery. Nic nie staje jednak na przeszkodzie abyś zaimplementował obsługę JSON-RPC w innym języku np. w Pythonie, Ruby czy Node.js. Mimo że istnieje wiele implementacji tego protokołu w różnych językach, pokażemy w jaki sposób napisać taką od zera.


Wiele frameworków czy to w JavaScript czy PHP implementujących architekturę [MVC](http://pl.wikipedia.org/wiki/Model-View-Controller) zawiera nieprawidłowy **Model**, który zazwyczaj jest po prostu abstrakcją nad danymi, a nie logiką aplikacji. Stosując zdalne procedury, które będą zawierały całą logikę aplikacji napisaną po stronie serwera oraz dodając **Widok** w postaci szablonów oraz **Kontroler** w JavaScript-cie otrzymujemy prawdziwą architekturę MVC, przy czym otrzymujemy jedną całość zawierająca klienta oraz serwer.

## Implementacja Serwera

Implementację obsługi protokołu zaczniemy od założenia, że nasze metody będą po prostu metodami jakiejś klasy, której instancję utworzymy. Dzięki temu będziemy mieli po prostu listę procedur (metod) i będziemy mogli zapomnieć o implementacji protokołu podczas pisania aplikacji.

{% highlight php startinline=true %}
class Service {
    function ping() {
        return 'pong';
    }
    function sum($a, $b) {
        return $a + $b;
    }
}

$service = new Service();
{% endhighlight %}

Nie napisaliśmy funkcji echo ponieważ, w języku php, jest to słowo zastrzeżone i nie może występować jako nazwa metody.

Napiszemy funkcję `json_rpc`, do której przekażemy instancję obiektu `Service`, którego referencja znajdującego się w zmiennej `$service`. W protokole **JSON-RPC** klient wysyła dane pod postacią obiektów JSON-a metodą POST, dlatego pierwszą rzeczą jak musimy zrobić to zdekodować dane przesłane od klienta. Nie możemy posłużyć się tablicą `$_POST`, ponieważ musimy mieć dostęp do surowych danych pod postacią tekstu, istnieje kilka sposobów pobrania takich danych w zależności od wersji php. My posłużymy się poniższym kodem, który powinien działać w różnych wersjach php.

{% highlight php startinline=true %}
if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
    $request = $GLOBALS['HTTP_RAW_POST_DATA']);
} else {
    $request = file_get_contents('php://input');
}
$request = json_decode($request);
{% endhighlight %}

Aby uprościć nasz przykładowy kod nie będziemy stosowali obsługi błędów, zakładamy że dane zawsze będą poprawnie sformatowanymi obiektami JSON-a, oraz że wszystkie dane o wywołanej procedurze będą się w nim znajdować.

Aby wywołać metodę obiektu musimy się posłużyć mechanizmem [refleksji](http://pl.wikipedia.org/wiki/Mechanizm_refleksji). Widząc jaką metodę chcemy wywołać, najpierw powinniśmy sprawdzić czy dana metodą istnieje, można tego dokonać za pomocą poniższego kodu:


{% highlight php startinline=true %}
$class_name = get_class($service);
$methods = get_class_methods($class_name);
$params_len = isset($request->params) ? 0 : count($request->params);
if (in_array($request->method, $methods)) {

}
{% endhighlight %}

Następnie musimy sprawdzić czy liczba parametrów się zgadza:

{% highlight php startinline=true %}
$method_object = new ReflectionMethod($class, $method);
$num_expect = $method_object->getNumberOfParameters();
$num_expect2 = $method_object->getNumberOfRequiredParameters();
if ($params_len == $num_expect || $params_len == $num_expect2) {

}
{% endhighlight %}

Aby wywołać metodę obiektu możemy posłużyć się poniższym wywołaniem:

{% highlight php startinline=true %}
$result = call_user_func_array(array($service, $request->method), $request->params);
{% endhighlight %}

Zmienna `$result` będzie zawierała wynik wywołania naszej metody, musimy tylko wyświetlić go jako poprawny obiekt JSON zgodny ze specyfikacją JSON-RPC.

{% highlight php startinline=true %}
header('Content-Type: application/json');

echo json_encode(array(
    'jsonrpc' => "2.0",
    'result' => $result,
    'id' => $request->id,
    'error' => null
));
{% endhighlight %}

Cały kod opakowany w funkcję będzie wyglądał jak poniżej:

{% highlight php startinline=true %}
function json_rpc($object) {
    if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
        $request = $GLOBALS['HTTP_RAW_POST_DATA']);
    } else {
        $request = file_get_contents('php://input');
    }
    $request = json_decode($request);
    $class = get_class($object);
    $methods = get_class_methods($class);
    $params_len = isset($request->params) ? count($request->params) : 0;
    if (in_array($request->method, $methods)) {
        $method_object = new ReflectionMethod($class, $request->method);
        $num_expect = $method_object->getNumberOfParameters();
        $num_expect2 = $method_object->getNumberOfRequiredParameters();
        if ($params_len == $num_expect || $params_len == $num_expect2) {
            // execute a function if number of params match
            $result = call_user_func_array(array($object, $request->method), $request->params);
            
            header('Content-Type: application/json');
            
            echo json_encode(array(
                'jsonrpc' => "2.0",
                'result' => $result,
                'id' => $request->id,
                'error' => null
            ));
        }
    }
}
{% endhighlight %}

Powyższa implementacja jest najmniejszym działającym przykładem. Aby uruchomić naszą usługę wystarczy wywołać naszą funkcję, przekazując do niej referencje do naszego obiektu:

{% highlight php startinline=true %}
json_rpc($service);
{% endhighlight %}

Do powyższego kodu moglibyśmy dodać obsługę błędów. Oprócz tych wynikających z błędnego wywołania serwer JSON-RPC powinien zwracać błąd gdy dana metoda nie istnieje lub gdy wywołano ją z inną niż wymagana liczbą parametrów. Powinno się móc wywołać metodę bez właściwości `params` gdy procedura (metoda) nie wymaga żadnych argumentów. Dodatkowo można zawrzeć wywołanie `call_user_func_array` wewnątrz instrukcji `try..catch`. W takim przypadku oprócz błędu protokołu można także zwrócić informację o wyjątku jaki został wyrzucony, dzięki czemu będziemy mogli zobaczyć jaki błąd wystąpił po stronie serwera bezpośrednio w kodzie klienta. Część błędów można także przechwycić za pomocą funkcji [`set_error_handler`](http://php.net/manual/en/function.set-error-handler.php), która zostanie wywołana w momencie wystąpienia błędu.

Przykładowy błąd w protokole JSON-RPC powinien wyglądać jak poniżej:

{% highlight javascript %}
{"jsonrpc": "2.0", "result": null, "id": 2, "error": {
    "code": -32601,
    "message": "There is no `foo' method"
}}
{% endhighlight %}

Do obiektu `error` można dodać drugą właściwość `error`, która będzie zawiera informację o błędzie php. Implementację obsługi błędów pozostawiam jako ćwiczenie dla czytelnika.

Jeśli będziemy chcieli po stronie klienta utworzyć automagicznie funkcje, które są dostępne w danej usłudze powinniśmy zwrócić informację o wszystkich dostępnych metodach. W specyfikacji (a właściwie szkicu) 1.1 istniała specjalna metoda `system.describe` która miała właśnie za zadanie zwrócenie informacji o wszystkich procedurach. Mimo że metoda ta nie znalazła się w specyfikacji 2.0 my utworzymy taką metodę. Poniższa funkcja zwróci nam tablicę, którą musimy zakodować do formatu JSON za pomocą funkcji `json_encode` w przypadku gdy nazwą zdalnej procedury będzie `system.describe`.

{% highlight php startinline=true %}
function service_description($object) {
    $class = get_class($object);
    $methods = get_class_methods($class);
    $service = array("sdversion" => "1.0",
                     "name" => "DemoService",
                     "address" => currentURL(),
                     "id" => "urn:md5:" . md5(currentURL()));
    $static = get_class_vars($class_name);
    foreach ($methods as $method_name) {
        $proc = array("name" => $method_name);
        $method = new ReflectionMethod($class, $method_name);
        $params = array();
        foreach ($method->getParameters() as $param) {
            $params[] = $param->name;
        }
        $proc['params'] = $params;
        $service['procs'][] = $proc;
    }
    return $service;
}
{% endhighlight %}

aby uzyskać unikalny identyfikator URN, posłużono się funkcją pobierającą aktualny adres url skryptu:

{% highlight php startinline=true %}
function currentURL() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
{% endhighlight %}


## Klient w JavaScript

Gdy mamy już naszą usługę oraz listę zdalnych procedur możemy utworzyć naszą usługę w JavaScript-cie. Utworzymy funkcję `JSON.rpc`, dzięki której otrzymamy obiekt, który będzie zawierał wszystkie zdalne procedury jako funkcje utworzone dynamicznie. Aby poradzić sobie z asynchronicznymi wywołaniami Ajax-owymi posłużymy się obiektami **jQuery Deferred**. Najpierw musimy przygotować małą funkcję, która utworzy zapytanie JSON-RPC.

{% highlight javascript %}
var id = 0;
function request(method, params) {
    return JSON.stringify({
        "method": method,
        "params": params,
        "id": id++,
        "jsonrpc": "2.0"
    });
}
{% endhighlight %}

Tak naprawdę identyfikatory nie będą nam potrzebne ponieważ zawsze gdy wywołujemy zdalną procedurę otrzymujemy właściwe zapytanie. Identyfikatory byłyby potrzebne gdybyśmy chcieli utworzyć wywołanie kilku metod w postaci tablicy.

Następnym krokiem będzie dynamiczne utworzenie funkcji bazując na danych otrzymanych z wywołania procedury `system.describe`:

{% highlight javascript %}
var serivce = {};
$.post(url, request('system.describe', []), function(response) {
    response.procs.forEach(function(proc) {
        serivce[proc.name] = function() {
            var deferr = new jQuery.Deferred();
            var args = [].slice.call(arguments);
            $.post(url, request(proc.name, args), function(response) {
                if (response.error) {
                    deferr.reject(response.error);
                } else {
                    deferr.resolve(response.result);
                }
            });
            return deferr.promise();
        };
    });
});
{% endhighlight %}

Nasza kompletna funkcja będzie wyglądała jak ponieżej:

{% highlight javascript %}
JSON.rpc = function(url) {
    var id = 0;
    function request(method, params) {
        return JSON.stringify({
            "method": method,
            "params": params,
            "id": id++,
            "jsonrpc": "2.0"
        });
    }
    var deferr = new jQuery.Deferred();
    $.post(url, request('system.describe', []), function(response) {
        var serivce = {};
        response.procs.forEach(function(proc) {
            serivce[proc.name] = function() {
                var deferr = new jQuery.Deferred();
                var args = [].slice.call(arguments);
                $.post(url, request(proc.name, args), function(response) {
                    if (response.error) {
                        deferr.reject(response.error);
                    } else {
                        deferr.resolve(response.result);
                    }
                });
                return deferr.promise();
            };
        });
        deferr.resolve(serivce); // resolve JSON.rpc deferred
    }, 'json');
    return deferr.promise();
};
{% endhighlight %}

Kod używajacy naszej funkcji będzie wyglądał tak jak na poniższym listingu:

{% highlight javascript %}
JSON.rpc('server.php').then(function(service) {
    console.log('ping');
    service.ping().then(function(result) {
        console.log(result);
        service.sum(10, 20).then(function(result) {
            console.log('sum is ' + result);
        });
    }); 
});
{% endhighlight %}

Nasz kod zakłada że obiekt JSON jest dostępny, jeśli chcielibyśmy aby nasz kod działał na przeglądarkach, w których jest on niedostępny musielibyśmy użyć biblioteki do obsługi JSON-a np. [tej napisanej przez Douglasa Crockforda](http://www.json.org/js.html) twórcy formatu JSON.

Teraz pozostaje już tylko pisanie metod w php i wywoływanie ich w JavaScript-cie.

Powyższa implementacja protokołu JSON-RPC jest niekompletna, jeśli ktoś byłby chętny taką utworzyć odsyłam do [specyfikacji 2.0](http://www.jsonrpc.org/specification).
