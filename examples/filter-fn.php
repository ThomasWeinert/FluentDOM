<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div id="first"></div>
  <div id="second"></div>
  <div id="third"></div>
  <div id="fourth"></div>
  <div id="fifth"></div>
  <div id="sixth"></div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//div')
  ->attr('border', 1)
  ->filter(
      function($node, $index) {
        if ($index == 1 || FluentDOM($node)->attr('id') == 'fourth') {
          return TRUE;
        }
        return FALSE;
      }
    )
  ->attr('style', 'text-align: center;');
?>