<?php
require_once(__DIR__.'/../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
$dom->registerNamespace('#default', 'http://www.w3.org/1999/xhtml');

$feed = $dom->appendElement('atom:feed');
$feed->applyNamespaces();
$feed->appendElement('atom:title', 'Example Feed');
$feed->appendElement('atom:link', ['href' => 'http://example.org/']);;

$entry = $feed->appendElement('atom:entry');
$entry->appendElement('atom:title', 'Atom-Powered Robots Run Amok');
$entry->appendElement('atom:id', 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
$entry->appendElement('atom:updated', '2003-12-13T18:30:02Z');
$entry->appendElement('atom:summary', ['type' => 'text/xhtml'])->appendElement('p', 'Some text.');

$dom->formatOutput = TRUE;
echo $dom->saveXml();


