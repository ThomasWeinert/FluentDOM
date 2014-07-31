<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');
require_once('../vendor/autoload.php');

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::insertBefore()</title>
  </head>
  <body>
    <div id="foo">FOO!</div><p>I would like to say: </p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->insertBefore('//div[@id = "foo"]');
