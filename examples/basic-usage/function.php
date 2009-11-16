<?php

require('../../FluentDOM.php');

$xmlFile = 'hello.xml';

echo FluentDOM($xmlFile)
  ->find('/message')
  ->text('Hello World!');