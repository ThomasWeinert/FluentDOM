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
    <title>Examples: FluentDOM\Query::text()</title>
  </head>
  <body>
    <div>
      <p>Hello</p>
      <p>cruel</p>
      <p>World!</p>
    </div>
  </body>
</html>
HTML;

/*
 * replace text content of 2nd paragraph
 */
echo FluentDOM($html)
  ->find('//p[position() = 2]')
  ->text('nice');

echo "\n\n";

/*
 * replace text content of every paragraph
 */
echo FluentDOM($html)
  ->find('//p')
  ->text('nice');

echo "\n\n";
