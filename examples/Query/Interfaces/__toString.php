<?php
require __DIR__.'/../../../vendor/autoload.php';

header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::__toString()</title>
  </head>
<body>
  <p>Hello</p><p>cruel</p><p>World</p>
</body>
</html>
XML;

echo FluentDOM($xml)
  ->find('//p')
  ->addClass('default')
  ->formatOutput();
