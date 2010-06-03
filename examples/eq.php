<?php
/**
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div></div>
  <div></div>
  <div></div>
  <div></div>
  <div></div>
  <div></div>
</body>
</html>
XML;

require_once('../src/FluentDOM.php');
echo FluentDOM($xml)
  ->find('//div')
  ->eq(2)
  ->addClass('emphased');
?>