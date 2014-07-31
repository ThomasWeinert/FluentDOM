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
HTML;

echo FluentDOM($html)
  ->add('<b id="sample">Paragraph. </b>')
  ->replaceAll('//p');
