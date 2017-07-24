<?php
require_once(__DIR__.'/../../vendor/autoload.php');

/*
 * Appending an element child is on of the most used features.
 *
 * appendElement() is a shortcut for appendChild() and
 * createElement().
 */

$document = new FluentDOM\DOM\Document();
$document->appendChild($document->createElement('div'));
$document->documentElement->appendElement(
  'span', 'Hello World!', ['class' => 'message']
);

echo $document->saveXML();
