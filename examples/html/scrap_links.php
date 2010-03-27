<?php
require('../../FluentDOM.php');

$html = file_get_contents('http://fluentdom.org/');

$links = FluentDOM($html, 'html')->find('//a[@href]')->map(
  function ($node) {
    return $node->getAttribute('href');
  }
);

var_dump($links);
?>