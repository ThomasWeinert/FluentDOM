<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::prepend()</title>
  </head>
  <body>
    <p> he said. </p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->prepend('<strong>Hello</strong>');
