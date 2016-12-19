<?php

require_once(__DIR__.'/../../vendor/autoload.php');

/*
 * FluentDOM\XmlSerializable is a interface supported by the trait
 * FluentDOM\XMLSerialize. It extends the FluentDOM\Appendable
 * interface with an getXml() method.
 */
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