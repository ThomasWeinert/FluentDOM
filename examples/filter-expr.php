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
  <div></div>
  <div class="middle"></div>
  <div class="middle"></div>
  <div class="middle"></div>
  <div class="middle"></div>
  <div></div>
</body>
</html>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//div')
  ->attr('border', 1)
  ->filter('@class = "middle"')
  ->attr('style', 'text-align: center;');
?>