<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::children()</title>
  </head>
  <body>
    <div id="container">
      <p>This <span>is the <em>way</em> we</span>
      write <em>the</em> demo,</p>
    </div>
  </body>
</html>
XML;

echo FluentDOM($xml)
  ->find('//div[@id = "container"]/p')
  ->children()
  ->toggleClass('child');

echo "\n\n";

echo FluentDOM($xml)
  ->find('//div[@id = "container"]/p')
  ->children('name() = "em"')
  ->toggleClass('child');