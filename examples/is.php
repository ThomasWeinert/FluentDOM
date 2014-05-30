<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::is()</title>
  </head>
  <body>
    <form><input type="checkbox" /></form>
    <div> </div>
  </body>
</html>
XML;

require_once('../vendor/autoload.php');
$dom = FluentDOM($xml);
$isFormParent = $dom
  ->find('//input[@type = "checkbox"]')
  ->parent()
  ->is('name() = "form"');
$dom
  ->find('//div')
  ->text('$isFormParent = '.($isFormParent ? 'TRUE' : 'FALSE'));

echo $dom;
