<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

require_once('../../vendor/autoload.php');
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
