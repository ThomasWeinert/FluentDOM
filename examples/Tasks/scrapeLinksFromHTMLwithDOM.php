<?php
//include FluentDOM
require('../../vendor/autoload.php');

$document = new \FluentDOM\DOM\Document();
$document->loadHTMLFile(__DIR__.'/example.html');

$links = [];
foreach ($document('//a[@href]/@href') as $href) {
  $links[] = (string)$href;
}

var_dump($links);
