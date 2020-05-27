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
(function() {
    if (navigator.share) {
        var button = document.querySelector('.share .mobile');
        if (button) {
            button.style.display = '';
            var web = document.querySelector('.mobile + .web');
            if (web) {
                web.style.display = 'none';
            }
            button.addEventListener('click', function() {
                var url = document.querySelector('head link[rel="canonical"]').href;
                var title = document.querySelector('head title').innerText;
                var text = "Zobacz ten wpis: ";

                navigator.share({
                    title: title,
                    text: text,
                    url: url
                }).then(function() { console.log('Successful share'); })
                    .catch(function(error) { console.log('Error sharing', error); });
            });
        }
    }
})();
// baner
if (console && console.log) {
    console.log([
        "%c               ,,      ,,                                 ,,",
        "  .g8\"\"\"bgd  `7MM     db                                  db",
        ".dP'     `M    MM'm",
        "dM'       `    MMm ,pW\"Wq.`7M'    ,A    `MF'`7MMpMMMb.  `7MM  .gP\"Ya",
        "MM            mMM 6W'   `Wb VA   ,VAA   ,V    MM    MM    MM ,M'   Yb",
        "MM.    `7MMF'm'MM 8M     M8  VA ,V  VA ,V     MM    MM    MM 8M\"\"\"\"\"\"",
        "`Mb.     MM    MM YA.   ,A9   VVV    VVV      MM    MM    MM YM.    ,",
        "  `\"bmmmdPY  .JMML.`Ybmd9'     W      W     .JMML  JMML..JMML.`Mbmmd'",
        "                                                               ,,",
        "   `7MMF'                          .M\"\"\"bgd                    db             mm",
        "     MM                           ,MI    \"Y                                   MM",
        "     MM  ,6\"Yb.`7M'   `MF',6\"Yb.  `MMb.      ,p6\"bo `7Mb,od8 `7MM `7MMpdMAo.mmMMmm",
        "     MM 8)   MM  VA   ,V 8)   MM    `YMMNq. 6M'  OO   MM' \"'   MM   MM   `Wb  MM",
        "     MM  ,pm9MM   VA ,V   ,pm9MM  .     `MM 8M        MM       MM   MM    M8  MM",
        "(O)  MM 8M   MM    VVV   8M   MM  Mb     dM YM.    ,  MM       MM   MM   ,AP  MM",
        " Ymmm9  `Moo9^Yo.   W    `Moo9^Yo.P\"Ybmmd\"   YMbmd' .JMML.   .JMML. MMbmmd'   `Mbmo"
    ].join('\n'), 'font-weight: bold; color: #152032');
}
