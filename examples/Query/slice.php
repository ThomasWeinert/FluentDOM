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
    <title>Examples: FluentDOM\Query::slice()</title>
  </head>
  <body>
    <div>
      <p>Hello</p>
      <p>cruel</p>
      <p>World!</p>
      <p>I am</p>
      <p>leaving</p>
      <p>you today!</p>
    </div>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->slice(0, 3)
  ->replaceAll('//div');

echo "\n\n";

echo FluentDOM($html)
  ->find('//p')
  ->slice(5, 2)
  ->replaceAll('//div');

echo "\n\n";

echo FluentDOM($html)
  ->find('//p')
  ->slice(1, -2)
  ->replaceAll('//div');

echo "\n\n";

/*
 * get all paragraphs after the first 3 of the document and replace every <div> element with them
 */
echo FluentDOM($html)
  ->find('//p')
  ->slice(3)
  ->replaceAll('//div');

