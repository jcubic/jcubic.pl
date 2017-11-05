---
layout: post
title:  "5 Parserów i Generatorów Parserów w JavaScript"
date:   2017-11-05 11:06:47+0100
categories:
tags:  javascript języki parsery biblioteki
author: jcubic
description: Jeśli myślałeś kiedyś o tym, aby napisać własny mini język, albo zadałeś sobie pytanie 'jak napisać parser w języku JavaScript?', odpowiedź brzmi...
---

Jeśli myślałeś kiedyś o tym, aby napisać własny mini język, albo zadałeś sobie pytanie "jak napisać parser w języku JavaScript?", odpowiedź brzmi: najlepiej użyć generatora parserów albo biblioteki parsera. W tym artykule przedstawię jak stworzyć parser wyrażeń arytmetycznych za pomocą 5 parserów i generatorów parserów.

<!-- more -->

## Nearley

[Nearly.js](https://nearley.js.org/) jest to biblioteka, w odróżnieniu od prawdziwych generatorów parserów,
mimo że generuje ona kod JavaScript, biblioteka jest także potrzebna aby uruchomić parser.

aby zainstalować bibliotekę należy z konsoli wywołać:

{% highlight bash %}
npm install nearley -g
{% endhighlight %}

aby wygenerować wynikowy plik JavaScript należy wykonać:

{% highlight bash %}
nearleyc grammar.ne > grammar.js
{% endhighlight %}

wynikiem będzie kod, który możemy użyć aby otrzymać parser:

{% highlight javascript %}
const grammar = require('./grammar);
const nearley = require('nearley');

const parser = new nearley.Parser(nearley.Grammar.fromCompiled(grammar));

try {
    parser.feed(process.argv[2]);
    console.log(parser.results[0]);
} catch(e) {
    console.log(e.message);
}
{% endhighlight %}

Wejściowa gramatyka może wyglądać tak:

```
{% raw %}
Exp -> _ AddExp _ {% function(data) { return data[1]; } %}

AddExp -> AddExp _ "+" _ MulExp {% function(data) { return data[0] + data[4]; } %}
       | AddExp _ "-" _ MulExp {% function(data) { return data[0] - data[4]; } %}
       | MulExp {% id %}

MulExp -> MulExp _ "*" _ factor {% function(data) { return data[0] * data[4]; } %}
       | MulExp _ "/" _ factor {% function(data) { return data[0] / data[4]; } %}
       | factor {% id %}

_ -> [\s]:*


number -> integer {% id %}
       | float {% id %}


integer -> [0-9]:+ {%
    function(data) {
        return parseInt(data[0].join(''));
    }
%}

float -> [0-9]:* "." [0-9]:+ {%
   function(data) {
        return parseFloat(data[0].join('') + '.' + data[2].join(''));
   }
%}

factor -> "(" Exp ")" {% function(data) { return data[1]; } %}
       | number {% id %}
{% endraw %}
```

Możesz zobaczyć demo na [CodePen](https://codepen.io/jcubic/pen/KyzYgJ?editors=0010)


## Peg.js

[Peg.js](https://pegjs.org/) jest to prawdziwy generator parserów, czyli otrzymujemy plik JavaScript,
który możemy użyć samodzielnie. Aby go zainstalować należy wykonać:

{% highlight bash %}
npm install pegjs -g
{% endhighlight %}

Aby utworzyć parser wykonujemy:

{% highlight bash %}
pegjs -o parser.js grammar.pegjs
{% endhighlight %}

Wynikowy plik jest to moduł wynikowy plik jest to moduł [CommonJS](https://en.wikipedia.org/wiki/CommonJS), wiec jest
przeznaczony aby uruchamiać go pod node.js, aby użyć go w przeglądarce należy użyć narzędzi:
[webpack](https://webpack.js.org/), [rollup.js](https://rollupjs.org/) albo [browserify](http://browserify.org/),
albo usunąć z końca pliku:

{% highlight javascript %}
module.exports = {
  SyntaxError: peg$SyntaxError,
  parse:       peg$parse
};
{% endhighlight %}

Wynikowa funkcja `peg$parse` to cały parser, przyjmuje ona łańcuch znaków i zwraca wartość, w przypadku niezgodności z
gramatyką wyrzuca wyjątek `peg$SyntaxError`. Oto przykładowy kod node.js, używający wynikowego parsera:

{% highlight javascript %}
const parser = require('./peg');

try {
    console.log(parser.parse(process.argv[2]));
} catch(e) {
    console.log(e.message);
}
{% endhighlight %}

Przykładowa gramatyka wygląda tak:

```
start
  = _ additive:additive _ { return additive; }

additive
  = left:multiplicative _ "+" _ right:additive { return left + right; }
  / left:multiplicative _ "-" _ right:additive { return left - right; }
  / multiplicative

multiplicative
  = left:primary _ "*" _ right:multiplicative { return left * right; }
  / left:primary _ "/" _ right:multiplicative { return left / right; }
  / primary

primary
  =  number
  / "(" _ additive:additive _ ")" { return additive; }

_
  = " "*

number
  = integer
  / float

integer "integer"
  = digits:[0-9]+ { return parseInt(digits.join('')); }

float
  = a:[0-9]* "." b:[0-9]+ { return parseFloat(a.join('') + '.' + b.join('')); }
```

Aby wypróbować bibliotekę Peg.js, możesz skorzystać z [narzędzia na oficjalnej stronie projektu](https://pegjs.org/online). Umożliwia ono także pobranie parsera, więc nie trzeba nic instalować aby go wypróbować.

## Jison

[Jison](http://zaa.ch/jison/) bazuje na generatorze parserów napisanego w C, czyli [Bison](https://pl.wikipedia.org/wiki/Bison_(program)) oraz leksera [flex](https://pl.wikipedia.org/wiki/Flex_(program)), które z kolei są wolnymi wersjami, dla [projekty GNU](https://www.gnu.org/), programów yacc oraz lex, które pierwotnie zostały napisane dla systemu operacyjnego UNIX.

Aby zainstalować program należy wykonać:

{% highlight bash %}
npm install jison -g
{% endhighlight %}

Aby wygenerować plik wynikowy używamy:

{% highlight bash %}
jison grammar.jison
{% endhighlight %}

Wynikiem będzie plik `grammar.js`, który tak jak w przypadku Peg.js jest samoistnym pakietem node.js, ale można go użyć bez zmian w przeglądarce. Dostajemy globalną zmienną `grammar`, która jest parserem. Wynikowy plik można także użyć jako programu wykonywalnego, który akceptuje nazwę pliku z "kodem źródłowym" naszego języka.

Kod użycia parsera jest taki sam jak w przypadku Peg.js.

Przykładowa gramatyka (wzięta z oficjalnych przykładów, tylko usunąłem wywołanie funkcji print) składa się z 3 części leksera, kolejności reguł oraz gramatyki:

```
%lex

%%
\s+                   /* pomijanie białych znaków */
[0-9]+("."[0-9]+)?\b  return 'NUMBER';
"*"                   return '*';
"/"                   return '/';
"-"                   return '-';
"+"                   return '+';
"^"                   return '^';
"("                   return '(';
")"                   return ')';
"PI"                  return 'PI';
"E"                   return 'E';
<<EOF>>               return 'EOF';

/lex

/* kolejność operatorów */

%left '+' '-'
%left '*' '/'
%left '^'
%left UMINUS

%start expressions

%% /* gramatyka/logika języka */

expressions
    : e EOF
        { return $1;}
    ;

e
    : e '+' e
        {$$ = $1+$3;}
    | e '-' e
        {$$ = $1-$3;}
    | e '*' e
        {$$ = $1*$3;}
    | e '/' e
        {$$ = $1/$3;}
    | e '^' e
        {$$ = Math.pow($1, $3);}
    | '-' e %prec UMINUS
        {$$ = -$2;}
    | '(' e ')'
        {$$ = $2;}
    | NUMBER
        {$$ = Number(yytext);}
    | E
        {$$ = Math.E;}
    | PI
        {$$ = Math.PI;}
    ;
```

Aby wypróbować bibliotekę Jison, możesz skorzystać z [narzędzia na oficjalnej stronie projektu](http://zaa.ch/jison/try/).

## OMeta/JS

[Ometa](https://en.wikipedia.org/wiki/OMeta) jest to parser, który ma implementacje w kilku językach programowania
m.i. w JavaScript. Oryginalna biblioteka chyba nie jest już utrzymywana, ale jest
[fork na githubie](https://github.com/veged/ometa-js) wraz z pakietem npm. (właściwie są dwa forki i dwa pakiety npm, ten drugi to [Page-/ometa-js](https://github.com/Page-/ometa-js), ale miałem problem z jego uruchomieniem)

Aby zainstalować bibliotekę należy wywołać:

{% highlight bash %}
npm install ometajs -g
{% endhighlight %}

Następnie, aby wygenerować plik JavaScript, należy wywołać:

{% highlight bash %}
ometajs2js -i grammar.ometajs > grammar.js
{% endhighlight %}

Aby użyć wygenerowanego parsera należy użyć:

{% highlight javascript %}
const parser = require('./ometa');

try {
    console.log(parser.Calc.matchAll(process.argv[2], 'expr'));
} catch(e) {
    console.log(e.message);
}
{% endhighlight %}

Wynikowy plik zależy od ometajs wiec musi być on zainstalowany gdy używamy biblioteki, aby użyć go w przeglądarce należy użyć narzędzia tak jak w przypadku Peg.js.

Przykładowa gramatyka:

```
ometa Calc {
  digit    = ^digit:d                                    -> parseInt(d),
  number = space* digits:n space*                        -> n,
  digits   = digits:n digit:d                            -> (n * 10 + d)
           | digit:d,
  addExpr  = addExpr:x '+' mulExpr:y                     -> (x + y)
           | addExpr:x '-' mulExpr:y                     -> (x - y)
           | mulExpr,
  mulExpr  = mulExpr:x '*' primExpr:y                    -> (x * y)
           | mulExpr:x '/' primExpr:y                    -> (x / y)
           | primExpr,
  primExpr = space* '(' space* expr:x space* ')' space*  -> x
           | number,
  expr     = addExpr
}
```

Możesz zobaczyć [demo na CodePen](https://codepen.io/jcubic/pen/OxebJr?editors=0010) przy czym korzysta ona z oryginalnej biblioteki a nie z forka. Aby z niej skorzystać w przeglądarce, trzeba dołączyć 7 plików JavaScript, korzystanie z forka jest o wiele łatwiejsze, przynajmniej z node.js.


## Ohm

[Ohm](https://github.com/harc/ohm) to parser bazujący na Ometa, Działa trochę inaczej. Logiki parsującej nie definiuje się w razem z regułami tylko w JavaScript-cie. Dodatkowo biblioteka nie generuje żadnego kodu i trzeba odwoływać się do pliku z gramatyką za każdym razem, gdy używamy parsera. Fajną cechą Ohm jest to, że białe znaki są ignorowane automatycznie, jeśli użyjemy reguł zaczynających się z dużej litery.

Aby zainstalować jak zwykle można użyć npm:


{% highlight bash %}
npm install ohm-js -g
{% endhighlight %}

Przykładowa gramatyka wygląda tak:

```
Arithmetic {
  Exp = AddExp

  AddExp = AddExp "+" MulExp  -- plus
         | AddExp "-" MulExp  -- minus
         | MulExp

  MulExp = MulExp "*" number  -- times
         | MulExp "/" number  -- div
         | number

  number = digit* "." digit+ -- float
         | digit+ -- integer
}

```


Aby użyć biblioteki z node.js można skorzystać z kodu poniżej:

{% highlight javascript %}
var ohm = require('ohm-js');
var fs = require('fs');
var contents = fs.readFileSync('grammar.ohm');
var grammar = ohm.grammar(contents);

var semantics = grammar.createSemantics();
semantics.addOperation('eval', {
    AddExp_plus: function(a, _, b) { // nazwa to: reguła _ <etykietka po znakach -- >
        return a.eval() + b.eval();
    },
    AddExp_minus: function(a, _, b) {
        return a.eval() - b.eval();
    },
    MulExp_times: function(a, _, b) {
        return a.eval() * b.eval();
    },
    MulExp_div: function(a, _, b) {
        return a.eval() / b.eval();
    },
    number: function(digits) {
        return parseFloat(digits.sourceString);
    }
});

console.log(semantics(grammar.match(process.argv[2])).eval());
{% endhighlight %}

Możesz też zobaczyć [demo na CodPen](https://codepen.io/jcubic/pen/GMbZvz?editors=0010).
