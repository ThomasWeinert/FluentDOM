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
    <title>Examples: FluentDOM\Query::not()</title>
  </head>
  <body>
    <div> </div>
    <div id="blueone"> </div>
    <div> </div>
    <div class="green"> </div>
    <div class="green"> </div>
    <div class="gray"> </div>
    <div> </div>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//div')
  ->not('@class = "green" or @id = "blueone"')
  ->addClass('blue');
