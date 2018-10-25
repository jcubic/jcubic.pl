---
layout: default
---
<section>
    <div class="search">
      <header><h2>Wyszukanie "<?= isset($_GET['q']) ? strip_tags($_GET['q']) : '' ?>"</h2></header>
<?php

function mark($query, $str) {
  return preg_replace("%(" . $query . ")%i", '<mark>$1</mark>', $str);
}

if (isset($_GET['q'])) {
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
        echo '<h3><a href="' . $row['url'] . '">' . mark($query, $row['title']) . '</a></h3>';
        $text = trim($match[0], " \t\n\r\0\x0B,.{}()-");
        echo '<p>' . mark($query, $text) . '</p>';
      }
    }
  }
}

?>
    </div>
</section>
