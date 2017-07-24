<?php
require_once(__DIR__.'/../../../vendor/autoload.php');

/*
 * The Optimize namespace transformer tries to reduce the namespace
 * definitions by moving them into ancestor nodes.
 *
 * It allows you to change the prefix for namespaces, too.
 */

$document = new FluentDOM\DOM\Document();
$document->preserveWhiteSpace = FALSE;
$document->loadXml(
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

/*
 * use the 'feed' prefix for the Atom namespace and no prefix
 * for XHTML.
 */
$transformer = new FluentDOM\Transformer\Namespaces\Optimize(
  $document,
  [
    'http://www.w3.org/2005/Atom' => 'feed',
    'http://www.w3.org/1999/xhtml' => ''
  ]
);
$target = $transformer->getDocument();
$target->formatOutput = TRUE;
echo "\nWith optimization, change atom to feed, set xhtml as default namespace:\n\n";
echo $target->saveXML();


/*
 * Use no prefix for any namespace.
 */
$transformer = new FluentDOM\Transformer\Namespaces\Optimize(
  $document,
  [
    'http://www.w3.org/2005/Atom' => '',
    'http://www.w3.org/1999/xhtml' => ''
  ]
);
$target = $transformer->getDocument();
$target->formatOutput = TRUE;
echo "\nWith optimization, no namespace prefixes:\n\n";
echo $target->saveXML();