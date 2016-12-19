<?php
//include FluentDOM
require('../../vendor/autoload.php');

$dom = new \FluentDOM\Document();
$dom->loadHTMLFile('http://fluentdom.org/');

$links = [];
foreach ($dom('//a[@href]/@href') as $href) {
  $links[] = (string)$href;
}

var_dump($links);
