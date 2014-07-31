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
    <title>Examples: FluentDOM\Query::next()</title>
  </head>
  <body>
    <div>
      <button disabled="disabled">First</button> <b>-</b> <span> </span></div>
    <div><button>Second</button> - <span> </span></div>
    <div><button disabled="disabled">Third</button> - <span> </span></div>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//button[@disabled]')
  ->next()
  ->next()
  ->text('This button is disabled.');
