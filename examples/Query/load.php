<?php
require __DIR__.'/../../vendor/autoload.php';

$document = new DOMDocument();
$document->loadXML('<message>Hello World!</message>');
$fd = new FluentDOM\Nodes();
$fd->load($document);

foreach ($fd->find('//message') as $message) {
  echo $message->nodeValue;
}

$json = '{ "message" : "Hello World!" }';
$fd = new FluentDOM\Nodes($json, 'text/json');
foreach ($fd->find('//message') as $message) {
  echo $message->nodeValue;
}
