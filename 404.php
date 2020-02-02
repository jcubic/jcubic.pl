<?php
header("HTTP/1.0 404 Not Found");
header_remove("X-Powered-By");
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>404 - File Not Found</title>
    <meta name="description" content="jcubic Server Error - File Not Found"/>
    <link rel="shortcut icon" href="/favicon/favicon.ico"/>
    <!--[if lt IE 9]>
    <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="/css/error.css" type="text/css" media="screen"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <header id="logo"><a href="https://jcubic.pl/"><img src="https://jcubic.pl/img/logo-blue.png" alt="jcubic logo"/></a></header>
    <section>
        <header>
            <div id="code">404</div>
            <div id="message">File Not Found</div>
        </header>
        <p>Sorry, but the file "<?= preg_replace("%/%", "/<wbr/>", $_SERVER['REQUEST_URI']); ?>"
   was not found on the <?= $_SERVER['HTTP_HOST']; ?> server.</p>
    </section>
    <footer>Copyright &copy; <?php  echo date('Y'); ?> Jakub Jankiewicz</footer>
    <!-- Matomo -->
    <script type="text/javascript">
      var _paq = window._paq || [];
      /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
          var u="//piwik.jcubic.pl/";
          _paq.push(['setTrackerUrl', u+'matomo.php']);
          _paq.push(['setSiteId', '2']);
          var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
          g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <!-- End Matomo Code -->
    <noscript>
      <!-- Matomo Image Tracker-->
      <img src="https://piwik.jcubic.pl/matomo.php?idsite=2&amp;rec=1" style="border:0" alt="" />
      <!-- End Matomo -->
    </noscript>
</body>
</html>
