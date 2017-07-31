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
    <title>Examples: FluentDOM\Query::insertBefore()</title>
  </head>
  <body>
    <div id="foo">FOO!</div><p>I would like to say: </p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->insertBefore('//div[@id = "foo"]');
