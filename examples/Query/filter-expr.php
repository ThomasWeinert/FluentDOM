<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
header('Content-type: text/plain');
require_once '../../vendor/autoload.php';

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::filter() with expression</title>
  </head>
  <body>
    <div/>
    <div class="middle"/>
    <div class="middle"/>
    <div class="middle"/>
    <div class="middle"/>
    <div/>
  </body>
</html>
HTML;

echo FluentDOM($html, 'text/html')
  ->find('//div')
  ->attr('border', 1)
  ->filter('@class = "middle"')
  ->attr('style', 'text-align: center;');
