<?php

require('../../FluentDOM.php');

$xmlFile = 'hello.xml';

// create and load
echo FluentDOM($xmlFile)
  // find root node
  ->find('/message')
  // replace text content
  ->text('Hello World!');