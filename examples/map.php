<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p><b>Values: </b></p>
  <form>
    <input type="text" name="name" value="John"/>
    <input type="text" name="password" value="password"/>
    <input type="text" name="url" value="http://ejohn.org/"/>
  </form>
</body>
</html>
XML;

require_once('../FluentDOM.php');
$dom = FluentDOM($xml);
echo $dom
  ->find('//p')
  ->append(
    implode(
      ', ',
      $dom
        ->find('//input')
        ->map(
          function($node) {
            return FluentDOM($node)->attr('value');
          }
        )
    )
  );
?>