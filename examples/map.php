<?php
/**
*
* @version $Id $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
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
        ->map('getNodeAttribValue')
    )
  );

/**
*
*
* @param $node
* @param $index
* @return string | array
*/
function getNodeAttribValue($node, $index) {
  return FluentDOM($node)->attr('value');
}
?>