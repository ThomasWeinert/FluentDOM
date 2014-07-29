<?php

require_once(__DIR__.'/../../vendor/autoload.php');

class Example implements \FluentDOM\XmlSerializable {

  use \FluentDOM\XmlSerialize;

  public function appendTo(\FluentDOM\Element $parent) {
    $parent->appendElement(
      'message',
      'Hello World!'
    );
  }
}
$example = new Example();
echo $example->getXml();