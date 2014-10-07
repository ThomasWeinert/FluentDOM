<?php
require(dirname(__FILE__).'/../../vendor/autoload.php');

$element = simplexml_load_string('<ul/>');
$li = $element->addChild('li', 'FluentDOM');
$li['href'] = 'http://fluentdom.org';

echo $element->saveXml();


$document = new FluentDOM\Document();
$ul = $document->appendElement('ul');
$li = $ul->appendElement('li', 'FluentDOM', ['href' => 'http://fluentdom.org']);

echo $document->saveHtml();

$_ = FluentDOM::create();
$document = $_(
  'ul',
  $_('li', ['href' => 'http://fluentdom.org'], 'FluentDOM')
)->document;
echo $document->saveHtml();

