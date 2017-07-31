<?php
require __DIR__.'/../../vendor/autoload.php';

$document = new FluentDOM\DOM\Document();
$document->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
$document->formatOutput = TRUE;

$feed = $document->appendElement('atom:feed');
$feed->appendElement('atom:title', 'Example Feed');
$feed->appendElement('atom:link', ['href' => 'http://example.org/']);
$feed->appendElement('atom:updated', '2003-12-13T18:30:02Z');
$author = $feed->appendElement('atom:author');
$author->appendElement('atom:name', 'John Doe');
$feed->appendElement('atom:id', 'urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
$entry = $feed->appendElement('atom:entry');
$entry->appendElement('atom:title', 'Atom-Powered Robots Run Amok');
$entry->appendElement('atom:link', ['href' => 'http://example.org/2003/12/13/atom03']);
$entry->appendElement('atom:id', 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
$entry->appendElement('atom:updated', '2003-12-13T18:30:02Z');
$entry->appendElement('atom:summary', 'Some text.');

echo $document->saveXML();
