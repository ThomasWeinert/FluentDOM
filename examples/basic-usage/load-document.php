<?php

require('../../vendor/autoload.php');

$xmlFile = 'hello.xml';

// create object
$fd = FluentDOM();
// use document attribute
$fd->document->load($xmlFile);

echo $fd
  ->find('/message')
  ->text('Hello World!');