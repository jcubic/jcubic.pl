<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function validate_code($code) {
    $items = array_filter(array_map(function($code) {
        if (preg_match("/^[a-z0-9]+$/i", $code)) {
            return "'$code'";
        }
    }, explode(",", $code)));
    return implode(",", array_slice($items, 0, 5));
}

header('Content-Type: application/javascript');

$partner = '12418M';

require('db.php');

$db = new PDO("sqlite:helion.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
if (isset($_GET['code'])) {
    $code = validate_code($_GET['code']);
    $query = "SELECT title, books.code, name as author, price FROM books left " .
        "join authors on author_id = authors.id where code in (" . $code . ")";
} else if ($_GET['category']) {
    $query = "SELECT title, books.code, name as author, price FROM books left " .
        "join authors on author_id = authors.id left join book_categories on " .
        "books.id = book_id left join categories on categories.id = cat_id " .
        "WHERE categories.label like '%" . $_GET['category'] . "%' and status = 1
        ORDER BY RANDOM() LIMIT 5";
}

if (isset($query)) {
   $rows = query($db, $query);
    $html = "<div class=\"book-ads-wrapper\">";
    foreach ($rows as $row) {
        $url = "http://helion.pl/view/$partner/${row['code']}.htm";
        $img = "https://static01.helion.com.pl/global/okladki/326x466/${row['code']}.jpg";
        $price = preg_replace("/.00$/", "", $row['price']);
        $html .= "<div class=\"book-buy\"><a href=\"$url\" target=\"_blank\" title=\"${row['title']}\">";
        $html .= "<img src=\"$img\" alt=\"Okładka książki: ${row['title']}\"/>";
        $html .= "</a><header><a href=\"$url\"><p class=\"title\">${row['title']}</p></a>";
        $html .= "<p class=\"author\">${row['author']}</p></header>";
        $html .= "<a href=\"$url\" class=\"buy\">Kup książkę</a>";
        $html .= "<p class=\"price\">${price}zł</p>";
        $html .= "</div>";
    }
    $html .= "</div>";
    echo "document.write('$html');";
}