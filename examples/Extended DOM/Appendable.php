<?php
require_once(__DIR__.'/../../vendor/autoload.php');

/*
 * FluentDOM\Appendable is an interface to allow you to build
 * objects that can be appended to a FluentDOM\Element.
 *
 * It allows you to build classes that can be included in an
 * XML serialization.
 */

class Example implements \FluentDOM\Appendable {

  public function appendTo(\FluentDOM\DOM\Element $parent) {
    $parent->appendChild(
      $parent->ownerDocument->createTextNode('Hello World!')
    );
  }
}

$document = new FluentDOM\DOM\Document();
$document->appendChild($document->createElement('xml'));
$document->documentElement->append(new Example());

echo $document->saveXml();
