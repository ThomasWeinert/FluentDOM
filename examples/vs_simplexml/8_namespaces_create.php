<?php
require(dirname(__FILE__).'/../../vendor/autoload.php');

$element = simplexml_load_string('<atom:feed xmlns:atom="http://www.w3.org/2005/Atom"/>');
$title = $element->addChild('atom:title', 'FluentDOM Feed', 'http://www.w3.org/2005/Atom');

echo $element->saveXml();

$document = new FluentDOM\Document();
$document->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
$document
  ->appendElement('atom:feed')
  ->appendElement('atom:title', 'FluentDOM Feed');
echo $document->saveXml();

$_ = FluentDOM::create();
$_->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
echo $_(
  'atom:feed',
  $_('atom:title', 'FluentDOM Feed')
);
