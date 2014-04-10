<?php
require_once(__DIR__.'/../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->registerNamespace('#default', 'http://www.w3.org/2005/Atom');

$feed = $dom->appendElement('feed');
$feed->appendElement('title', 'Example Feed');
$feed->appendElement('link', NULL, ['href' => 'http://example.org/']);
$feed->appendElement('updated', '2003-12-13T18:30:02Z');
$feed->appendElement('author')->appendElement('name', 'John Doe');
$feed->appendElement('id', 'urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');

$entry = $feed->appendElement('entry');
$entry->appendElement('title', 'Atom-Powered Robots Run Amok');
$entry->appendElement('link', NULL, ['href' => 'http://example.org/2003/12/13/atom03']);
$entry->appendElement('id', 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
$entry->appendElement('updated', '2003-12-13T18:30:02Z');
$entry->appendElement('summary', 'Some text.');

$dom->formatOutput = TRUE;
echo $dom->saveXml();


