<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
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

require_once '../../vendor/autoload.php';
echo FluentDOM($xml)
  ->find('//div[@id = "container"]/p')
  ->children()
  ->toggleClass('child');

echo "\n\n";

echo FluentDOM($xml)
  ->find('//div[@id = "container"]/p')
  ->children('name() = "em"')
  ->toggleClass('child');