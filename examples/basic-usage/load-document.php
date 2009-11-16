<?php

require('../../FluentDOM.php');

$xmlFile = 'hello.xml';

$fd = new FluentDOM();
$fd->documnet->load($xmlFile);

echo $fd
  ->find('/message')
  ->text('Hello World!');