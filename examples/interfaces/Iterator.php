<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p>Hello</p>
  <p>cruel</p>
  <p>World</p>
</body>
</html>
XML;

require_once('../../FluentDOM.php');

foreach (FluentDOM($xml)->find('//p') as $key => $value) {
  echo $key, ': ', $value->nodeName, "\n";
}
?>