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
    <title>Examples: FluentDOM\Query::is()</title>
  </head>
  <body>
    <form><input type="checkbox" /></form>
    <div> </div>
  </body>
</html>
HTML;

$fd = FluentDOM($html, 'text/html');
var_dump(
  $fd
    ->find('//input[@type = "checkbox"]')
    ->parent()
    ->is('name() = "form"')
);

