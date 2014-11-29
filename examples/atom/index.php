<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>Simple Atom Reader - Sample Script</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <style type="text/css">
      body {
        font-family: sans-serif;
        background-color: #FFF;
        color: #000;
      }
      a {
        color: #060;
      }
      div.entry {
        clear: both;
        padding-top: 2em;
      }
      ul.categories li {
        float: left;
        margin-left: 20px;
      }
    </style>
  </head>
  <body>
    <?php
require_once('../../vendor/autoload.php');
$dom = FluentDOM('./atom-sample.xml');
$dom->registerNamespace('atom', 'http://www.w3.org/2005/Atom');

$categories = array_unique(
  $dom
    ->find('//atom:category')
    ->map(
      function (\DOMElement $node) {
        return $node->getAttribute('term');
      }
    ));
if (count($categories) > 0) {
  echo '<ul class="categories">'."\n";
  foreach ($categories as $category) {
    printf(
      '<li><a href="?label=%s">%s</a></li>'."\n",
      urlencode($category),
      htmlspecialchars($category)
    );
  }
  echo '</ul>'."\n";
}

if (empty($atomGET['label'])) {
  $expr = '//atom:entry';
} else {
  $expr = '//atom:entry[atom:category[@term = "'.htmlspecialchars($_GET['label']).'"]]';
}
foreach ($dom->find($expr) as $entryNode) {
  echo '<div class="entry">'."\n";
  $entry = FluentDOM($entryNode);
  printf(
    '<h2><a href="%s">%s</a></h2>'."\n",
    htmlspecialchars(
      $entry->find('.//atom:link[@rel = "alternate" and @type = "text/html"]')->attr('href')
    ),
    htmlspecialchars($entry->find('.//atom:title')->text())
  );
  $summary = $entry->find('.//atom:summary|.//atom:content');
  switch ($summary->attr('type')) {
  case 'html' :
  case 'text/html' :
    //in real world you would need to use a purifier
    printf(
      '<div class="summary">%s</div>'."\n",
      $summary->text()
    );
    break;
  case 'text' :
  case 'text/plain' :
  case '' :
    printf(
      '<div class="summary">%s</div>'."\n",
      htmlspecialchars($summary->text())
    );
    break;
  default :

    break;
  }
  echo '</div>'."\n";
}
    ?>
  </body>
</html>
