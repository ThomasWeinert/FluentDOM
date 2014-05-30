<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::replaceAll()</title>
  </head>
  <body>
    <div>
      <p>Hello</p>
      <p>cruel</p>
      <p>World</p>
    </div>
  </body>
</html>
XML;

require_once('../vendor/autoload.php');
$doc = FluentDOM($xml);
echo FluentDOM($xml)
  ->add('<b id="sample">Paragraph. </b>')
  ->replaceAll('//p');
