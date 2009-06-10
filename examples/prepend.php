<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p> he said. </p>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->prepend('<strong>Hello</strong>');
?>