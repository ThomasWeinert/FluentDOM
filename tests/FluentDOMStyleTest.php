<?php
require_once 'PHPUnit/Framework.php';
require_once '../FluentDOMStyle.php';

/**
 * Test class for FluentDOMStyle.
 */
class FluentDOMStyleTest extends PHPUnit_Framework_TestCase {

  const HTML = '
    <html>
      <body>
        <div></div>
      </body>
    </html>
  ';
  
  function testConstructor() {
    $doc = new FluentDOMStyle(self::HTML);
    $this->assertTrue($doc instanceof FluentDOMStyle);
  }
  
  function testChaining() {
    $doc = FluentDOMStyle(self::HTML);
    $this->assertTrue($doc instanceof FluentDOMStyle);
    $findDoc = $doc->find('//div');
    $this->assertTrue($findDoc instanceof FluentDOMStyle);
    $this->assertTrue($doc !== $findDoc);
  }
}
?>
