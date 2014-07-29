<?php

$xml = <<<'XML'
<items>
  <item></item>
  <item></item>
  <item></item>
</items>
XML;

require_once(__DIR__.'/../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->preserveWhiteSpace = FALSE;
$dom->loadXML($xml);

var_dump(count($dom->documentElement));
