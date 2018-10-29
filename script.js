/* global localStorage */
function hide_baner() {
    document.querySelector('.cookie-baner').style.display = 'none';
}
if (localStorage.getItem('cookie')) {
    hide_baner();
}
window.onload = function() {
    document.getElementById('ok').addEventListener('click', function() {
        localStorage.setItem('cookie', true);
        hide_baner();
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
    window.addEventListener('scroll', function() {
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
