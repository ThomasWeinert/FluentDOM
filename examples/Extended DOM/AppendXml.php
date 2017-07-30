<?php
require_once __DIR__.'/../../vendor/autoload.php';

/*
 * Append XML fragments with a simple function.
 */
$document = new FluentDOM\DOM\Document();
$document->appendChild($document->createElement('div'));
$document->documentElement->appendXml(
  'Hello <b>World</b>!'
);

echo $document->saveXML();
