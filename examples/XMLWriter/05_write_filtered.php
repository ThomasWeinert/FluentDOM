<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

require __DIR__.'/../../vendor/autoload.php';

$xml = <<<'XML'
<persons>
  <person><name>Alice</name></person>
  <person><name>Bob</name></person>
  <person><name>Charlie</name></person>
</persons>
XML;

// Create the target writer and add the root element
$writer = new \FluentDOM\XMLWriter();
$writer->openUri('php://stdout');
$writer->setIndent(2);
$writer->startDocument();
$writer->startElement('persons');

// load the source into a reader
$reader = new \FluentDOM\XMLReader();
$reader->open('data://text/plain;base64,'.base64_encode($xml));

// iterate the person elements - the iterator expands them into a DOM node
foreach (new FluentDOM\XMLReader\SiblingIterator($reader, 'person') as $person) {
  /** @var \FluentDOM\DOM\Element $person */
  // ignore "Bob"
  if ($person('string(name)') !== 'Bob') {
    // write expanded node to the output
    $writer->collapse($person);
  }
}

$writer->endElement();
$writer->endDocument();
