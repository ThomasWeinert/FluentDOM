<?php

require('../../src/FluentDOM.php');

$xmlFile = 'hello.xml';

// prepare
$dom = new DOMDocument();
$dom->load($xmlFile);

// load
echo FluentDOM::Query($dom)
  ->find('/message')
  ->text('Hello World!');