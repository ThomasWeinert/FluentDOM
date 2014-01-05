<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query IteratorAggregate interface</title>
  </head>
<body>
  <p>Hello</p>
  <p>cruel</p>
  <p>World</p>
</body>
</html>
XML;

require_once('../../src/FluentDOM.php');

foreach (FluentDOM::Query($xml)->find('//p') as $key => $value) {
  echo $key, ': ', $value->nodeName, "\n";
}
