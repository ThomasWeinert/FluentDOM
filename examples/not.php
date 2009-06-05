<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div></div>
  <div id="blueone"></div>
  <div></div>
  <div class="green"></div>
  <div class="green"></div>
  <div class="gray"></div>
  <div></div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//div')
  ->not('@class = "green" or @id = "blueone"')
  ->addClass('blue');
?>