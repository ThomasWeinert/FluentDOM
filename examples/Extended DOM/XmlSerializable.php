<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

require __DIR__.'/../../vendor/autoload.php';

/*
 * FluentDOM\XmlSerializable is a interface supported by the trait
 * FluentDOM\XMLSerialize. It extends the FluentDOM\Appendable
 * interface with an getXml() method.
 */
class Example implements \FluentDOM\XmlSerializable {

  use \FluentDOM\XmlSerialize;

  public function appendTo(\FluentDOM\DOM\Element $parentNode) {
    $parentNode->appendElement(
      'message',
      'Hello World!'
    );
  }
}
$example = new Example();
echo $example->getXml();
