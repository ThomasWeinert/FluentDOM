<?php
require_once(__DIR__.'/../../vendor/autoload.php');

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

$transformer = new FluentDOM\Transformer\Namespaces\Optimize(
  $dom,
  [
    'http://www.w3.org/2005/Atom' => 'feed',
    'http://www.w3.org/1999/xhtml' => ''
  ]
);
$target = $transformer->getDocument();
$target->formatOutput = TRUE;
echo "\nWith optimization, change atom to feed, set xhtml as default namespace:\n\n";
echo $target->saveXML();

$transformer = new FluentDOM\Transformer\Namespaces\Optimize(
  $dom,
  [
    'http://www.w3.org/2005/Atom' => '',
    'http://www.w3.org/1999/xhtml' => ''
  ]
);
$target = $transformer->getDocument();
$target->formatOutput = TRUE;
echo "\nWith optimization, no namespace prefixes:\n\n";
echo $target->saveXML();