<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query Countable interface</title>
  </head>
<body>
  <p>Hello</p>
  <p>cruel</p>
  <p>World</p>
</body>
</html>
XML;

require_once('../../src/FluentDOM.php');

echo count(FluentDOM::Query($xml)->find('//p')), ' <p> tags';
