<?php

require_once(__DIR__.'/../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->appendChild($dom->createElement('div'));
$dom->documentElement->appendElement(
  'span', 'Hello World!', ['class' => 'message']
);

echo $dom->saveXML();
