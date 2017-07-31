<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Nodes::toArray()</title>
  </head>
<body>
  <p>Hello</p>
  <p>cruel</p>
  <p>World</p>
</body>
</html>
XML;

$nodes = FluentDOM($xml)->find('//p')->toArray();
array_splice($nodes, 1, 1);
echo implode(' ', $nodes);

