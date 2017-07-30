<?php
require_once __DIR__.'/../../vendor/autoload.php';

$xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>Example Feed</title>
  <link href="http://example.org/"/>
  <updated>2003-12-13T18:30:02Z</updated>
  <author>
    <name>John Doe</name>
  </author>
  <id>urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6</id>
  <entry>
    <title>Atom-Powered Robots Run Amok</title>
    <link href="http://example.org/2003/12/13/atom03"/>
    <id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
    <updated>2003-12-13T18:30:02Z</updated>
    <summary>Some text.</summary>
  </entry>
</feed>
XML;

/*
 * FluentDOM allows you to use array syntax on the element nodes.
 *
 * If the offset is an integer or (a string that contains only digits) it
 * will return the child node with that offset.
 *
 * Otherwise it will return the attribute value.
 */

$document = new FluentDOM\DOM\Document();
$document->preserveWhiteSpace = FALSE;
$document->loadXML($xml);
$document->registerNamespace('atom', 'http://www.w3.org/2005/Atom');

foreach ($document->evaluate('//atom:entry/atom:link') as $entry) {
  echo $entry['href'], "\n";
}