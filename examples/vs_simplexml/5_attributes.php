<?php
require(dirname(__FILE__).'/../../vendor/autoload.php');

$xml = <<<'XML'
<foo attribute="value"/>
XML;

$element = simplexml_load_string($xml);
echo $element['attribute'], "\n";

$element = FluentDOM::load($xml)->documentElement;
echo $element['attribute'], "\n";