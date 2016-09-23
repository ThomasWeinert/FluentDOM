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
    <title>Examples: FluentDOM\Query::is()</title>
  </head>
  <body>
    <form><input type="checkbox" /></form>
    <div> </div>
  </body>
</html>
HTML;

$dom = FluentDOM($html, 'text/html');
var_dump(
  $dom
    ->find('//input[@type = "checkbox"]')
    ->parent()
    ->is('name() = "form"')
);

