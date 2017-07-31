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
    <title>Examples: FluentDOM\Query::prependTo()</title>
  </head>
  <body>
    <div id="foo">Yellow! </div>
    <span>He thought </span>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//span')
  ->prependTo('//div[@id = "foo"]');
