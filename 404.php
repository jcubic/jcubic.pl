<?php
header("HTTP/1.0 404 Not Found");
header_remove("X-Powered-By");
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8"/>
    <title>404 - File Not Found</title>
    <meta name="description" content="jcubic Server Error - File Not Found"/>
    <link rel="shortcut icon" href="/favicon/favicon.ico"/>
    <!--[if lt IE 9]>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="http://jcubic.pl/css/error.css" type="text/css" media="screen"/>
</head>
<body>
    <header id="logo"><a href="http://jcubic.pl/"><img src="http://jcubic.pl/img/logo-blue.png" alt="jcubic logo"/></a></header>
    <section>
        <header>
            <div id="code">404</div>
            <div id="message">File Not Found</div>
        </header>
        <p>Sorry, but the file "<?= $_SERVER['REQUEST_URI']; ?>"
   was not found on the <?= $_SERVER['HTTP_HOST']; ?> server.</p>
    </section>
    <footer>Copyright &copy; <?php  echo date('Y'); ?> Jakub Jankiewicz</footer>
</body>
</html>
