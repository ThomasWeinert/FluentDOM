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
    <title>Examples: FluentDOM\Query::prevAll()</title>
  </head>
  <body>
    <div><span>has child</span></div>
    <div id="start"> </div>
    <div> </div>
    <div><span>has child</span></div>
    <div class="here"><span>has child</span></div>
    <div><span>has child</span></div>
    <div class="here"> </div>
    <div> </div>
    <p><button>Go to Prev</button></p>
  </body>
</html>
HTML;

echo FluentDOM($html)
  ->find('//div[@id = "start"]')
  ->prevAll()
  ->addClass('before');

echo "\n\n";

echo FluentDOM($html)
  ->find('//div[@class= "here"]')
  ->prevAll('.//span')
  ->addClass('nextTest');
