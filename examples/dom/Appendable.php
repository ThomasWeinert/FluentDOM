<?php
require_once(__DIR__.'/../../vendor/autoload.php');

class Example implements \FluentDOM\Appendable {

  public function appendTo(\FluentDOM\Element $parent) {
    $parent->appendChild(
      $parent->ownerDocument->createTextNode('Hello World!')
    );
  }
}

$dom = new FluentDOM\Document();
$dom->appendChild($dom->createElement('xml'));
$dom->documentElement->append(new Example());

echo $dom->saveXml();
