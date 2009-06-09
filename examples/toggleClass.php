<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p class="blue">foo</p>
  <p class="blue highlight">bar</p>
</body>
</html>

XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->toggleClass('highlight')
?>