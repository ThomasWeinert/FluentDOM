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
    <p>World!</p>
    <p>I am</p>
    <p>leaving</p>
    <p>you today!</p>
  </div>
</body>
</html>
XML;

require_once('../FluentDOM.php');

/*
 * get first 3 paragraphs of the document and replace every <div> element with them
 */
echo FluentDOM($xml)
  ->find('//p')
  ->slice(0,3)
  ->replaceAll('//div');

echo "\n\n";

echo FluentDOM($xml)
  ->find('//p')
  ->slice(5,2)
  ->replaceAll('//div');

echo "\n\n";

echo FluentDOM($xml)
  ->find('//p')
  ->slice(1,-2)
  ->replaceAll('//div');

echo "\n\n";

/*
 * get all paragraphs after the first 3 of the document and replace every <div> element with them
 */
echo FluentDOM($xml)
  ->find('//p')
  ->slice(3)
  ->replaceAll('//div');

?>