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
  <div>
    <p>Hello</p>
    <p>cruel</p>
    <p>World</p>
  </div>
</body>
</html>
XML;

$samples = <<<XML
<samples>
  <b id="first">Paragraph. </b>
</samples>
XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->node(
    FluentDOM($samples)
    ->find('//b[@id = "first"]')
    ->removeAttr('id')
    ->addClass('imported')
  )
  ->replaceAll('//p');
?>