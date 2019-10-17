<?php
/*
 * Script that create SQLite databse from Helion xml files
 *
 * Copyright (C) 2019 Jakub T. Jankiewicz
 * released under Creative Commons Attribution Share-Alike license (CC-BY-SA)
 *
 * run this from CLI because it take a while to run
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -----------------------------------------------------------------------------
function validate_files() {
    $files = array(
        'products-all.xml',
        'statusy-helion.xml',
        'lista-katalog.cgi.xml'
    );
    $missing = false;
    foreach ($files as $file) {
        if (!file_exists($file)) {
            echo "File $file is required to generate sqlite database\n";
            flush();
            $missing = true;
            
        }
    }
    if ($missing) {
        echo "To get xml files visit https://program-partnerski.helion.pl/\n";
        die();
    }
}

// -----------------------------------------------------------------------------
// :: function convert simple xml object (from attributions call) to array
// -----------------------------------------------------------------------------
function attrs_to_array($attr) {
    $result = array();
    foreach ($attr as $key => $value) {
        $result[(string)$key] = (string)$value;
    }
    return $result;
}
// -----------------------------------------------------------------------------
// :: function return list of book statuses based on xml file
// -----------------------------------------------------------------------------
function parse_status() {
    $xml = new SimpleXMLElement(file_get_contents('statusy-helion.xml'));
    $status = array();
    foreach ($xml->lista->ksiazka as $book) {
        $attr = attrs_to_array($book->attributes());
        $status[$attr['ident']] = $attr;
    }
    return $status;
}
// -----------------------------------------------------------------------------
// :: function return list of categories from xml file
// -----------------------------------------------------------------------------
function parse_cat() {
    $xml = new SimpleXMLElement(file_get_contents('lista-katalog.cgi.xml'));
    $categories = array();
    foreach ($xml->item as $category) {
        $attr = attrs_to_array($category->attributes());
        $attr['description'] = (string)$category;
        $categories[] = $attr;
    }
    return $categories;
}
// -----------------------------------------------------------------------------
// :: function return list of categories from book (it return null category
// :: when xml should have category) it return only categories from helion
// -----------------------------------------------------------------------------
function categories($book) {
    $categories = array();
    if ($book->categories) {
        foreach ($book->categories->category as $category) {
            $attr = $category->attributes();
            $site = (string)$attr['serwis'];
            $id = intval($attr['id']);
            
            if ($site == 'helion') {
                $categories[] = array(
                    'id' => $id,
                    'label' => (string)$category
                );
            }
        }
        if (count($categories) == 0) {
            // check for broken xml file - not specified helion category
            if (intval($book->marka) == 1) {
                $categories[] = array(
                    'id' => 'null',
                    'label' => 'Undefined'
                );
            }
        }
    }
    return $categories;
}

// -----------------------------------------------------------------------------
// :: function create one level of nesting categories
// -----------------------------------------------------------------------------
function build_categories() {
    global $db;
    $categories = parse_cat();

    $parents = array();
    $parent_query = "INSERT INTO categories(code, key, label) VALUES (?, ?, ?)";
    $child_query = "INSERT INTO categories(code, parent_id, key, label) SELECT".
        " ?, id, ?, ? FROM categories WHERE code = ?";
    foreach($categories as $cat) {
        
        if (!in_array($cat['id_nad'], $parents)) {
            $parents[] = $cat['id_nad'];
            query($db, $parent_query, array(
                intval($cat['id_nad']),
                $cat['seo_nad'],
                $cat['grupa_nad']
            ));
        }
        query($db, $child_query, array(
            intval($cat['id_pod']),
            $cat['seo_pod'],
            $cat['grupa_pod'],
            intval($cat['id_nad'])
        ));
    }
}
// -----------------------------------------------------------------------------
// :: main function that add books authors and proper join M->N table for categories
// -----------------------------------------------------------------------------
function build_books() {
    global $xml;
    global $partner;
    $stat = parse_status();
    global $db;
    $author_query = "INSERT INTO authors(name) VALUES(?)";
    $book_query = "INSERT INTO books(code, title, description, status, price, " .
        "author_id) SELECT ?, ?, ?, ?, ?, id FROM authors WHERE name = ?";
    $authors = array();
    $cat_query = "INSERT INTO book_categories(book_id, cat_id) SELECT books.id,".
        "(SELECT categories.id FROM categories WHERE categories.code = ?) FROM ".
        "books WHERE books.code = ?";
    foreach ($xml->ksiazka as $book) {
        $code = (string)$book->attributes()['ident'];
        $title = trim($book->title);
        $cover = trim($book->okladka);
        $author = trim($book->autor);
        $categories = categories($book);
        $description = trim($book->opis);
        if (count($categories) > 0 && isset($stat[$code])) {
            $status = $stat[$code];
            if (!in_array($author, $authors)) {
                query($db, $author_query, array($author));
                $authors[] = $author;
            }
            echo json_encode(array(
                'code' => $code,
                'title' => $title,
                'cover' => $cover,
                'categories' => $categories
            )) . "\n";
            flush();
            
            query($db, $book_query, array(
                $code,
                $title,
                $description,
                intval($status['status_online']),
                $status['cena_online'], $author
            ));
            foreach($categories as $category) {
                query($db, $cat_query, array($category['id'], $code));
            }

        }
    }
}

// -----------------------------------------------------------------------------
// :: main code
// -----------------------------------------------------------------------------

validate_files();
$xml = new SimpleXMLElement(file_get_contents('products-all.xml'));

// -----------------------------------------------------------------------------
// :: DB init
// -----------------------------------------------------------------------------
$file = 'helion.db';
if (file_exists($file)) {
    unlink($file);
}

require('db.php');

$db = new PDO("sqlite:$file");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// -----------------------------------------------------------------------------
// :: table creation
// -----------------------------------------------------------------------------
query($db, "CREATE TABLE authors(id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(500))");
query($db, "CREATE TABLE books(id INTEGER PRIMARY KEY AUTOINCREMENT, code VARCHAR(10), " .
    "title VARCHAR(300), description TEXT, status INTEGER, price VARCHAR(10), author_id" .
    " INTEGER, FOREIGN KEY(author_id) REFERENCES authors(id))");
query($db, "CREATE TABLE categories(id INTEGER PRIMARY KEY AUTOINCREMENT, code INTEGER,".
    "parent_id INTEGER DEFAULT NULL, key VARCHAR(50), label VARCHAR(200))");
query($db, "CREATE TABLE book_categories(cat_id INTEGER, book_id INTEGER, FOREIGN KEY".
    "(cat_id) REFERENCES categories(id), FOREIGN KEY(book_id) REFERENCES books(id))");

// -----------------------------------------------------------------------------
// :: category for broken books in xml
// -----------------------------------------------------------------------------
query($db, "INSERT INTO categories(code, key, label) VALUES('null', 'null', 'Undefined')");

echo "build categories ...";
flush();
build_categories();
echo "OK\nBuild Books ...";
flush();
build_books();
echo "OK\n";

?>