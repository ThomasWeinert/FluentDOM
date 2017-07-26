<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
header('Content-type: text/plain');
require_once('../../vendor/autoload.php');

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::empty()</title>
  </head>
  <body>
    <p class="first">Hello One</p>
    <p>Hello Two</p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p[@class = "first"]')
  ->empty();
