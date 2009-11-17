<?php

require('../../FluentDOM.php');

$xmlFile = 'hello.xml';

// prepare
$dom = new DOMDocument();
$dom->load($xmlFile);

// load
echo FluentDOM($dom)
  ->find('/message')
  ->text('Hello World!');