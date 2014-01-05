<?php

require('../../src/FluentDOM.php');

$xmlFile = 'hello.xml';

// create and load
echo FluentDOM::Query($xmlFile)
  // find root node
  ->find('/message')
  // replace text content
  ->text('Hello World!');