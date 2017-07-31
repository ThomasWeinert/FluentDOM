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
    <title>Examples: FluentDOM\Query::remove()</title>
  </head>
  <body>
    <p class="first">Hello One</p>
    <p>Hello Two</p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p[@class = "first"]')
  ->remove();
