<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div>
    <p>Hello</p>
    <p>cruel</p>
    <p>World</p>
  </div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
$doc = FluentDOM($xml);
echo FluentDOM($xml)
  ->node('<b id="sample">Paragraph. </b>')
  ->replaceAll('//p');
?>