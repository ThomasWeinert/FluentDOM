<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$xml = <<<'XML'
<items>
  <item>One</item>
  <item>Two</item>
  <item>Three</item>
</items>
XML;

echo FluentDOM($xml)
  ->find('//item[contains(.,"Two")]')
  ->index();
