---
layout: default
---
<section>
    <div class="search">
      <header><h1>Wyszukanie słowa "<?= isset($_GET['q']) ? strip_tags($_GET['q']) : '' ?>"</h1></header>
<?php

function mark($query, $str) {
  return preg_replace("%(" . $query . ")%i", '<mark>$1</mark>', $str);
}

if (isset($_GET['q']) && !empty($_GET['q'])) {
  $db = new PDO('sqlite:index.db');
  $stmt = $db->prepare('SELECT * FROM page WHERE content LIKE :var OR title LIKE :var');
  $wildcarded = '%'. $_GET['q'] .'%';
  $stmt->bindParam(':var', $wildcarded); // trzeba użyć zmiennej w tym miejscu
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $query = str_replace("%", "\\%", preg_quote($_GET['q']));
  $re = "%(?>\S+\s*){0,10}(" . $query . ")\s*(?>\S+\s*){0,10}%i";
  if (count($data) == 0) {
    echo "<p>Brak wyników</p>";
  } else {
    foreach ($data as $row) {
      if (preg_match($re, $row['content'], $match)) {
        echo '<h2><a href="' . $row['url'] . '">' . mark($query, $row['title']) . '</a></h2>';
        $text = trim($match[0], " \t\n\r\0\x0B,.{}()-");
        echo '<p>' . mark($query, $text) . '</p>';
      }
    }
  }
} else {
  echo '<p>Brak wyników - Puste zapytanie spróbuj wyszukać <a href="search.php?q=javascript">javascript</a></p>';
}

?>
    </div>
</section>
