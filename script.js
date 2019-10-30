/* global localStorage, setTimeout */
function display_baner(value) {
    document.querySelector('.cookie-baner').style.display = value;
}
if (!localStorage.getItem('cookie')) {
    display_baner('');
}
window.onload = function() {
    document.getElementById('ok').addEventListener('click', function() {
        localStorage.setItem('cookie', true);
        display_baner('none');
    });
    // jekyll+pygment+babylon lexer don't support async await
    var nx = document.querySelectorAll('.nx');
    for (var i in nx) {
        if (['async', 'await'].indexOf(nx[i].innerHTML) !== -1) {
            nx[i].className = 'k';
        }
    }
    var body = document.body,
        html = document.documentElement;

    var height = window.innerHeight;
    var aside = document.querySelector('aside');
    var pos = window.scrollY || window.scrollTop || document.getElementsByTagName("html")[0].scrollTop;
    var rect = aside.getBoundingClientRect();
    var bottom = rect.bottom + pos;
    var section = document.querySelector('#inner > section:last-of-type');
    window.addEventListener('scroll', function() {
        if (section.clientHeight < aside.clientHeight) {
            return;
        }
        var pos = window.scrollY || window.scrollTop || document.getElementsByTagName("html")[0].scrollTop;
        if (aside.classList) {
            if (pos + height > bottom) {
                aside.classList.add('sticky');
            } else {
                aside.classList.remove('sticky');
            }
        } else if (pos + height > bottom) {
            if (!aside.className.match(/sticky/)) {
                aside.className += ' sticky';
            }
        } else {
            aside.className = aside.className.replace(/\s*sticky/, '');
        }
    });
};
function loadCSS(src) {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = src;
    link.type = 'text/css';
    var head = document.getElementsByTagName('head')[0];
    head.appendChild(link);
}

loadCSS('https://fonts.googleapis.com/css?family=Muli:400,400i,700|Roboto:500&subset=latin,latin-ext&display=swap');
Array.from(document.querySelectorAll('link[data-href]')).forEach(function(link) {
    link.href = link.getAttribute('data-href');
});
