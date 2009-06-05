<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div id="container">
    <p>This <span>is the <em>way</em> we</span> 
    write <em>the</em> demo,</p>
  </div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//div[@id = "container"]/p')
  ->children()
  ->toggleClass('child');
?>