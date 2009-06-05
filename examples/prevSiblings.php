<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div></div>
  <div><span>has child</span></div>
  <div id="start"></div>
  <div></div>
  <p><button>Go to Prev</button></p>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//div[@id = "start"]')
  ->prevSiblings()
  ->addClass('before');
?>