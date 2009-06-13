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
  <div><span>has child</span></div>
  <div id="start"></div>
  <div></div>
  <div><span>has child</span></div>
  <div class="here"><span>has child</span></div>
  <div><span>has child</span></div>
  <div class="here"></div>
  <div></div>
  <p><button>Go to Prev</button></p>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//div[@id = "start"]')
  ->prevAllSiblings()
  ->addClass('before');

echo "\n\n";

echo FluentDOM($xml)
  ->find('//div[@class= "here"]')
  ->prevAllSiblings('.//span')
  ->addClass('nextTest');
?>