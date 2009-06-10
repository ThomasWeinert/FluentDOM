<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <ul>
    <li>One</li>
    <li>Two</li>
    <li class="hilite">Three</li>
    <li>Four</li>
  </ul>
  <ul>
    <li>Five</li>
    <li>Six</li>
    <li>Seven</li>
  </ul>
  <ul>
    <li>Eight</li>
    <li class="hilite">Nine</li>
    <li>Ten</li>
    <li class="hilite">Eleven</li>
  </ul>
  <p>Unique siblings: <b></b></p>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//li[@class = "hilite"]')
  ->siblings()
  ->addClass('before');

/*echo "\n\n";

echo FluentDOM($xml)
  ->find('//div[@class= "here"]')
  ->prevSiblings('.//span')
  ->addClass('nextTest');*/
?>