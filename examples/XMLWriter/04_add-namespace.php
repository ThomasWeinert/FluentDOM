<?php
require __DIR__.'/../../vendor/autoload.php';

$data = [
  'one' => '<e:persons xmlns:e="urn:example"><e:person><e:name>Alice</e:name></e:person></e:persons>',
  'two' => '<persons><person><name>Bob</name></person><person><name>Charlie</name></person></persons>'
];

// Create the target writer and add the root element
$writer = new \FluentDOM\XMLWriter();
$writer->openURI('php://stdout');
$writer->registerNamespace('p', 'urn:persons');
$writer->setIndent(2);
$writer->startDocument();
$writer->startElement('p:persons');

// iterate the example sources
foreach ($data as $key => $xml) {
  // load the source into a reader
  $reader = new \FluentDOM\XMLReader();
  $reader->open('data://text/plain;base64,' . base64_encode($xml));

  // iterate the person elements
  foreach (new FluentDOM\XMLReader\SiblingIterator($reader, 'person') as $person) {
    // use the transformer to move the nodes into the namespace
    $writer->collapse(
      new \FluentDOM\Transformer\Namespaces\Replace(
        $person,
        // namespaces to replace
        ['' => 'urn:persons', 'urn:example' => 'urn:persons'],
        // prefix for target namespace
        ['urn:persons' => 'p']
      )
    );
  }
}

$writer->endElement();
$writer->endDocument();