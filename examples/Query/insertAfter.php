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
    <title>Examples: FluentDOM\Query::insertAfter()</title>
  </head>
  <body>
    <p> is what I said... </p><div id="foo">FOO!</div>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//p')
  ->insertAfter('//div[@id = "foo"]');
