<?php

require('../../src/_require.php');

$xmlFile = 'hello.xml';

// create object
$fd = new FluentDOM\Query();
// load file
$fd->load($xmlFile);

echo $fd
  ->find('/message')
  ->text('Hello World!');