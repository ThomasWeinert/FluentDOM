<?php
require_once(__DIR__.'/../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->loadXml('<xml>Hello World</xml>');
$xpath = new FluentDOM\Xpath($dom);
echo $xpath->firstOf('//xml');


$value = "World";
$dom = new FluentDOM\Document();
$dom->loadXml('<xml>Hello World</xml>');
$xpath = new FluentDOM\Xpath($dom);
echo $xpath->evaluate(
  'string(//xml[contains(., '.$xpath->quote($value).')])'
);