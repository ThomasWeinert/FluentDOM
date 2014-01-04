<?php
/**
* Example file for function 'attr'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<html:html xmlns:html='http://www.w3.org/1999/xhtml'>
 <html:head>
  <html:title>Examples: FluentDOM\Query::attr()</html:title>
 </html:head>
 <html:body>
  <html:p>
   Always nice to visit
   <html:a href='http://fluentdom.org'>here.</html:a>
  </html:p>
 </html:body>
</html:html>
XML;


echo "Example for function 'attr' using XML namespaces:\n\n";
require('../src/FluentDOM.php');
$dom = FluentDOM::Query($xml);
$dom->registerNamespace('h', 'http://www.w3.org/1999/xhtml');
echo $dom
  ->find('//h:a')
  ->attr('href');
