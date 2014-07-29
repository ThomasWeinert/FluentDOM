<?php

require_once('../vendor/autoload.php');

$dom = new DOMDocument();
$dom->loadXML('<message>Hello World!</message>');
$fd = new FluentDOM\Nodes();
$fd->load($dom);

foreach ($fd->find('//message') as $message) {
  echo $message->nodeValue;
}

$json = '{ "message" : "Hello World!" }';
$fd = new FluentDOM\Nodes($json, 'text/json');
foreach ($fd->find('//message') as $message) {
  echo $message->nodeValue;
}
