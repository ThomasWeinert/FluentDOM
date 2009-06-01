<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div>first</div>
  <div>sibling<div>child</div></div>
  <div>sibling</div>
  <div>sibling</div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//div[position() = 1]')
  ->nextAllSiblings()
  ->addClass('after')
  ->document
  ->saveXML();
?>