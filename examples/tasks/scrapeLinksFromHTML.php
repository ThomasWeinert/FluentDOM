<?php
//include FluentDOM
require('../../FluentDOM.php');

//load data from an url
$html = file_get_contents('http://www.papaya-cms.com/');

$links = FluentDOM($html, 'html')
  // find links
  ->find('//a[@href]')
  // map nodes to array elements
  ->map(
    function ($node) {
      return $node->getAttribute('href');
    }
  );

var_dump($links);
?>