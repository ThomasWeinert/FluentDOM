<?php

require('../../FluentDOM.php');

$xmlFile = 'hello.xml';

// create object
$fd = new FluentDOM();
// load file
$fd->load($xmlFile);

echo $fd
  ->find('/message')
  ->text('Hello World!');