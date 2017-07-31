<?php
require __DIR__.'/../../vendor/autoload.php';

//load data from an url
$html = file_get_contents('https://www.heise.de/');

$links = FluentDOM($html, 'text/html')
  // find links
  ->find('//a[@href]')
  // map nodes to array elements
  ->map(
    function (DOMElement $node) {
      return $node->getAttribute('href');
    }
  );

var_dump($links);
