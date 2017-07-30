<?php
require __DIR__.'/../../vendor/autoload.php';

$data = [
  'one' => '<persons><person><name>Alice</name></person></persons>',
  'two' => '<persons><person><name>Bob</name></person><person><name>Charlie</name></person></persons>'
];

// Create the target writer and add the root element
$writer = new \FluentDOM\XMLWriter();
$writer->openURI('php://stdout');
$writer->setIndent(2);
$writer->startDocument();
$writer->startElement('persons');

// iterate the example sources
foreach ($data as $key => $xml) {
  // load the source into a reader
  $reader = new \FluentDOM\XMLReader();
  $reader->open('data://text/plain;base64,' . base64_encode($xml));

  // iterate the person elements
  foreach (new FluentDOM\XMLReader\SiblingIterator($reader, 'person') as $person) {
    // collapse the element into the target - you could use DOM methods to modify it
    $writer->collapse($person);
  }
}

$writer->endElement();
$writer->endDocument();