<?php

require_once(__DIR__.'/../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->appendChild($dom->createElement('div'));
$dom->documentElement->appendXml(
  'Hello <b>World</b>!'
);

echo $dom->saveXML();
