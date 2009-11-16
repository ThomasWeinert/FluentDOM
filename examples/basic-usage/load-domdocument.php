<?php

require('../../FluentDOM.php');

$xmlFile = 'hello.xml';

$dom = new DOMDocument();
$dom->load($xmlFile);

echo FluentDOM($dom);
  ->find('/message')
  ->text('Hello World!');