<?php
require_once(__DIR__.'/../../vendor/autoload.php');

$xml = <<<'XML'
<items>
  <item></item>
  <item></item>
  <item></item>
</items>
XML;

/*
 * Return the child node count.
 * Basically a shortcut to DOMNode::$childNodes::$length.
 */

$document = new FluentDOM\Document();
$document->preserveWhiteSpace = FALSE;
$document->loadXML($xml);

var_dump(count($document->documentElement));
