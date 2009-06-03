<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>Atom Reader Sample Script</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
  </head>
  <body>
    <?php
      require_once('../../FluentDOM.php');
      $dom = new FluentDOM(file_get_contents('atom-sample.xml'));
      
      $categories = array_unique($dom->find('//_:category')->map('callbackCategoryTerm'));
      if (count($categories) > 0) {
        echo '<ul class="categories">';
        foreach ($categories as $category) {
          printf(
            '<li><a href="?label=%s">%s</a></li>',
            urlencode($category),
            htmlspecialchars($category)
          );
        }
        echo '</ul>';
      }
      
      if (empty($_GET['label'])) {
        $expr = '//_:entry';
      } else {
        $expr = '//_:entry[_:category[@term = "'.htmlspecialchars($_GET['label']).'"]]';
      }
      foreach ($dom->find($expr) as $entryNode) {
        $entry = FluentDOM($entryNode);
        printf(
          '<h2><a href="?entry=%s&amp;label=%s">%s</a></h2>',
          htmlspecialchars($entry->find('.//_:id')->text()),
          htmlspecialchars($label),
          htmlspecialchars($entry->find('.//_:title')->text())
        );
      }
    ?>
  </body>
</html>
<?php
  function callbackCategoryTerm($node) {
    return $node->getAttribute('term');
  }
?>