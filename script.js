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
