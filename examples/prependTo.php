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
  <div id="foo">Yellow! </div>
  <span>He thought </span>
</body>
</html>
XML;

require_once('../src/FluentDOM.php');
echo FluentDOM($xml)
  ->find('//span')
  ->prependTo('//div[@id = "foo"]');
?>