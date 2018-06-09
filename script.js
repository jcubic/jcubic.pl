/* global localStorage */
function hide_baner() {
    document.querySelector('.cookie-baner').style.display = 'none';
}
if (localStorage.getItem('cookie')) {
    hide_baner();
}
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
