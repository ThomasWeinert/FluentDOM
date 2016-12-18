<?php
require(__DIR__.'/../../vendor/autoload.php');

$xml = <<<'XML'
<foo attribute="value">
  <one></one>
  <two></two>
</foo>
XML;

/*
 * SimpleXML allows to use Array Syntax to access the attribute of an element node.
 */
$element = simplexml_load_string($xml);
echo $element['attribute'], "\n";

/*
 * If you use string FluentDOM does also, ...
 */
$element = FluentDOM::load($xml)->documentElement;
echo $element['attribute'], "\n";

/*
 * ... but if it is an integer it will access the child nodes.
 */
$element = FluentDOM::load($xml)->documentElement[1];
echo $element->localName, "\n";