<?php
require_once(__DIR__.'/../../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->preserveWhiteSpace = FALSE;
$dom->loadXml(
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
  $dom,
  [
    'http://www.w3.org/1999/xhtml' => ''
  ]
);
$target = $transformer->getDocument();
$target->formatOutput = TRUE;
echo "\nRemove the xhtml namespace:\n\n";
echo $target->saveXML();

$transformer = new FluentDOM\Transformer\Namespaces\Replace(
  $dom,
  [
    'http://www.w3.org/2005/Atom' => 'urn:atom',
    'http://www.w3.org/1999/xhtml' => 'urn:xhtml'
  ]
);
$target = $transformer->getDocument();
$target->formatOutput = TRUE;
echo "\nReplace namespaces:\n\n";
echo $target->saveXML();