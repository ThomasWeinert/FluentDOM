<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div>
    <p>
      <span>
        <b>My parents are: </b>
      </span>
    </p>
  </div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
$dom = FluentDOM($xml);
$parents = implode(
  ', ',
  $dom
    ->find('//b')
    ->parents()
    ->map(
      function ($node) {
        return $node->tagName; 
      }
    )
);
echo $dom
  ->find('//b')
  ->append('<strong>'.htmlspecialchars($parents).'</strong>')
  ->document
  ->saveXML();
?>