<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p>I would like to say: </p>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->append('<strong>Hello</strong>');
?>