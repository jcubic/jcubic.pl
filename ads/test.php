<!DOCTYPE html>
<html>
<head>
<style>
.book-ads-wrapper {
    display: flex;
}
.book-buy {
    font-size: 12px;
    text-align: center;
    font-family: sans-serif;
    vertical-align: top;
    align-items: center;
    display: inline-flex;
    flex-direction: column;
    width: 170px;
}
.book-buy img {
    max-width: 200px;
    width: 80%;
}
.book-buy header p {
    margin: 0;
}
.book-buy .title {
    font-weight: bold;
    font-size: 1.2em;
    max-width: 250px;
    padding: 0 1em;
}
.book-buy header {
    margin: 0.5em 0;
    flex: 1;
}
.book-buy .buy {
    background: #008000;
    color: #fff;
    font-size: 1.5em;
    padding: 0.3em 0.4em;
    display: inline-block;
    border-radius: 4px;
    text-decoration: none;
    margin: 0.3em 0;
}
.book-buy .buy:hover {
    text-decoration: underline;
}
.book-buy .price {
    font-size: 2em;
    font-weight: bold;
    margin-top: 5px;
    margin-bottom: 0;
}
</style>
</head>
<body>
<!--
    <div class="book-buy">
        <a href="https://helion.pl/view/12418M/unszpr.htm"
           target="_blank" title="UNIX. Sztuka programowania">
           <img src="https://static01.helion.com.pl/global/okladki/326x466/unszpr.jpg"
                alt="Okładka książki: UNIX. Sztuka programowania"/>
        </a>
        <header>
            <p class="title">
                UNIX. Sztuka Programowania
            </p>
            <p class="author">
                Eric S. Raymond
            </p>
        </header>
        <a href="https://helion.pl/view/12418M/unszpr.htm"
           class="buy">Kup książkę</a>
        <p class="price">49zł</p>
    </div>
    
    <p>Lorem Ipsum</p>
    <script src="http://localhost/projects/jcubic/www/blog/reklama/ad.php?code=unszpr,deshak"></script>
    <p>Dolor Sit Amet</p>
    <script src="http://localhost/projects/jcubic/www/blog/reklama/ad.php?category=javascript"></script>
    -->
    <script src="helion.php?<?= $_SERVER['QUERY_STRING'] ?>"></script>
</body>
</html>