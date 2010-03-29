<?php

require('../../FluentDOM.php');

$xmlFile = 'hello.xml';

// create object
$fd = new FluentDOM();
// use document attribute
$fd->document->load($xmlFile);

echo $fd
  ->find('/message')
  ->text('Hello World!');