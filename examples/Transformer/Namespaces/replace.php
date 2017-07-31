<?php
require __DIR__.'/../../../vendor/autoload.php';

/*
 * Teh replace namespace transformer replaces namespaces. It basically
 * recreates the nodes for the new namespace. This will not directly
 * affect the namespace prefixes.
 */

$document = new FluentDOM\DOM\Document();
$document->preserveWhiteSpace = FALSE;
$document->loadXML(
  '<?xml version="1.0" encoding="UTF-8"?>
  <atom:feed xmlns:atom="http://www.w3.org/2005/Atom">
    <atom:title>Example Feed</atom:title>
    <atom:entry>
      <atom:title>Atom-Powered Robots Run Amok</atom:title>
      <atom:summary type="text/xhtml">
        <xhtml:p xmlns:xhtml="http://www.w3.org/1999/xhtml">Some text.</xhtml:p>
      </atom:summary>
    </atom:entry>
  </atom:feed>'
);

$transformer = new FluentDOM\Transformer\Namespaces\Replace(
  $document,
  [
    'http://www.w3.org/1999/xhtml' => ''
  ]
);
$target = $transformer->getDocument();
$target->formatOutput = TRUE;
echo "\nRemove the xhtml namespace:\n\n";
echo $target->saveXML();

$transformer = new FluentDOM\Transformer\Namespaces\Replace(
  $document,
  [
    'http://www.w3.org/2005/Atom' => 'urn:atom',
    'http://www.w3.org/1999/xhtml' => 'urn:xhtml'
  ]
);
$target = $transformer->getDocument();
$target->formatOutput = TRUE;
echo "\nReplace namespaces:\n\n";
echo $target->saveXML();