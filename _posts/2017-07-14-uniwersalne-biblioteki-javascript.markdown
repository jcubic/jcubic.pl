---
layout: post
title:  "Uniwersalne biblioteki JavaScript czyli UMD"
date:   2017-07-14 17:08:37+0200
categories:
tags:  javascript node.js umd modules
author: jcubic
description: UMD czyli Uniwersalna definicja modułu (ang. Universal Module Definition) jest to sposób tworzenia definicji modułu/biblioteki JavaScript aby była możliwość korzystania z niej wszędzie.
sitemap:
  lastmod: 2017-07-14 17:08:37+0200
---

UMD czyli Uniwersalna definicja modułu (ang. Universal Module Definition) jest to sposób tworzenia definicji modułu/biblioteki JavaScript aby była możliwość korzystania z niej wszędzie.

<!-- more -->

Jeśli tworzymy bibliotekę JavaScript to warto zapewnić że będzie możliwość jej użycia w każdej sytuacji, czyli  w przeglądarce lub na serwerze (node.js). Korzystając np. z [require.js](http://www.requirejs.org/) czyli definicji [AMD](https://github.com/amdjs/amdjs-api/wiki/AMD) lub [CommonJS](http://wiki.commonjs.org/wiki/CommonJS).

Jeśli nasza biblioteka tworzy nowy obiekt np. `foo`, który chcemy używać "wszędzie", to możemy użyć poniższego kodu:

{% highlight javascript %}
(function (root, factory) {
    if (typeof define === "function" && define.amd) {
        define(["underscore"], factory);
    } else if (typeof exports === "object") {
        module.exports = factory(require("underscore"));
    } else {
        root.foo = factory(root._);
    }
}(typeof this == 'undefined' ? window : this, function (_) {
    var foo = {
        // nasza definicja obiektu foo
        bar: function() {
            console.log('bar');
        }
    };

    return foo;
}));
{% endhighlight %}

Nasza biblioteka posiada jedną zaleźnośc ([underscore](http://underscorejs.org/)), w przypadku gdy nasza biblioteka nie ma zależności wystarczy poniższy kod:

{% highlight javascript %}
(function (root, factory) {
    if (typeof define === "function" && define.amd) {
        define([], factory);
    } else if (typeof exports === "object") {
        module.exports = factory();
    } else {
        root.foo = factory();
    }
}(typeof this == 'undefined' ? window : this, function () {
    var foo = {
        // nasza definicja obiektu foo
        bar: function() {
            console.log('bar');
        }
    };

    return foo;
}));
{% endhighlight %}

Nasz obiekt `foo` będzie dodany do obiektu globalnego, czyli `window` w przeglądarce, gdy dodamy nasz moduł za pomocą tagu `script`:

{% highlight html %}
<script src="foo.js"></script>
<script>
foo.bar();
</script>
{% endhighlight %}

Będziemy mogli go załadować przez require.js czyli:

{% highlight javascript %}
requirejs(['foo'], function(foo) {
    foo.bar();
});
{% endhighlight %}

lub w node.js w poniższy sposób:

{% highlight javascript %}
var foo = require('./foo');
foo.bar();
{% endhighlight %}

Jeśli będziemy korzystali z biblioteki [Babeljs](https://babeljs.io/), aby pisać w ES6, to wtedy będziemy mogli użyć poniższego kodu:

{% highlight javascript %}
import foo from './foo';
foo.bar();
{% endhighlight %}

A wszystko to dzięki temu, że dodaliśmy trochę więcej kodu do naszej biblioteki. Więcej szablonów dotyczących UMD można znaleźć na [stronie projektu na githubie](https://github.com/umdjs/umd/).

