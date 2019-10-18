<!DOCTYPE html>
<html>
<head>
    <title>Helion Books</title>
    <link rel="stylesheet" href="../css/style.css"/>
    <style>
@media (max-width: 700px) {
    .book-buy:nth-child(1) {
        display: none;
    }
}
@media (max-width: 600px) {
    .book-buy:nth-child(2) {
        display: none;
    }
}

@media (max-width: 500px) {
    .book-buy:nth-child(3) {
        display: none;
    }
}
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <script src="helion.php?<?= $_SERVER['QUERY_STRING'] ?>"></script>
</body>
</html>