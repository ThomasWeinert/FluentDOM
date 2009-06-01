<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <form><input type="checkbox" /></form>
  <div></div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
$dom = FluentDOM($xml);
$isFormParent = $dom
  ->find('//input[@type = "checkbox"]')
  ->parent()
  ->is('name() = "form"');
$dom
  ->find('//div')
  ->text('$isFormParent = '.($isFormParent ? 'TRUE' : 'FALSE'));

echo $dom->document->saveXML();
?>