<?php
/*
 * Ads serving script, the file is using like JavaScript
 *
 * Copyright (C) 2019 Jakub T. Jankiewicz
 * released under Creative Commons Attribution Share-Alike license (CC-BY-SA)
 */
define('__PARTNER__', '12418M'); // Helion partner Code
define('__LIMIT__', 5); // limit number of books
define('__DEBUG__', true);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -----------------------------------------------------------------------------
// :: return list of strings to be used with 'in' sql operator
// -----------------------------------------------------------------------------
function validate_code($code) {
    $items = array_filter(array_map(function($code) {
        if (preg_match("/^[a-z0-9]+$/i", $code)) {
            return "'$code'";
        }
    }, explode(",", $code)));
    return implode(",", array_slice($items, 0, 5));
}
// -----------------------------------------------------------------------------
// :: function that return list of OR condition to be used in where
// :: it need to be in parenthesis if using and after the call
// :: the sql code will check if any colum match any of the strings in $q
// :: $q is string with comma sparated values
// -----------------------------------------------------------------------------
function like($column, $q) {
    $q = explode(",", $q);
    return implode(" or ", array_map(function($column) use ($q) {
        $cond = array_map(function($q) use ($column) {
            return "$column like '%$q%'";
        }, $q);
        return implode(" or ", $cond);
    }, $column));
}
// -----------------------------------------------------------------------------
// :: remove cache - script tags are cached even if it's php file
// -----------------------------------------------------------------------------
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Content-Type: application/javascript; charset=utf-8');

// -----------------------------------------------------------------------------
// :: init db
// -----------------------------------------------------------------------------
require('db.php');

$db = new PDO("sqlite:helion.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// -----------------------------------------------------------------------------
// :: generate query
// -----------------------------------------------------------------------------
if (isset($_GET['code'])) {
    $code = validate_code($_GET['code']);
    $query = "SELECT title, books.code, name as author, price FROM books left " .
        "join authors on author_id = authors.id where code in (" . $code . ")";
} elseif (isset($_GET['category'])) {
    $query = "SELECT DISTINCT title, books.code, name as author, price FROM books " .
        "left join authors on author_id = authors.id left join book_categories on " .
        "books.id = book_id left join categories c1 on c1.id = cat_id " .
        "inner join categories c2 on c2.id = c1.parent_id " .
        "WHERE (" . like(array('c1.label', 'c2.label'), $_GET['category']) . " ) " .
        "and status = 1 ORDER BY RANDOM() LIMIT " . __LIMIT__;
} elseif (isset($_GET['q'])) {
    $query = "SELECT DISTINCT title, books.code, name as author, price FROM books left " .
        "join authors on author_id = authors.id left join book_categories on " .
        "books.id = book_id left join categories c1 on c1.id = cat_id " .
        "inner join categories c2 on c2.id = c1.parent_id " .
        "WHERE (" . like(array('title'), $_GET['q']) . " ) " .
        "and status = 1 ORDER BY RANDOM() LIMIT " . __LIMIT__;
}
if (__DEBUG__) {
    echo "/* $query */";
}

// -----------------------------------------------------------------------------
// :: run query and create list of books (based on query) as JavaScript code
// -----------------------------------------------------------------------------
if (isset($query)) {
    $partner = __PARTNER__;
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
        $html .= "<a href=\"$url\" class=\"buy\">Zobacz</a>";
        $html .= "<p class=\"price\">${price}zł</p>";
        $html .= "</div>";
    }
    $html .= "</div>";
    echo "document.write('$html');";
}
