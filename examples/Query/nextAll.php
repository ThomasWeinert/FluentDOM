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
    <title>Examples: FluentDOM\Query::nextAll()</title>
  </head>
  <body>
    <div>first</div>
    <div>sibling<div>child</div></div>
    <div>sibling</div>
    <div>sibling</div>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//div[position() = 1]')
  ->nextAll()
  ->addClass('after');
