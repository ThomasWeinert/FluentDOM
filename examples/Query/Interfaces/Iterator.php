<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query Iterator interface</title>
  </head>
<body>
  <p>Hello</p>
  <p>cruel</p>
  <p>World</p>
</body>
</html>
XML;

require_once '../../../vendor/autoload.php';

foreach (FluentDOM($xml)->find('//p') as $key => $value) {
  echo $key, ': ', $value, "\n";
}
