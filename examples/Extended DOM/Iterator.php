<?php
require __DIR__.'/../../vendor/autoload.php';

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
 * Element nodes use the childNodes property node list for iteration,
 * but FluentDOM implements the iterator on the element node itself.
 */

$document = new FluentDOM\DOM\Document();
$document->preserveWhiteSpace = FALSE;
$document->loadXML($xml);

foreach ($document->documentElement as $node) {
  echo get_class($node), " ";
  if ($node instanceof DOMElement) {
    echo ' - ', $node->nodeName;
  } elseif ($node instanceof DOMText) {
    echo ': ', $node->nodeValue;
  }
  echo "\n";
}