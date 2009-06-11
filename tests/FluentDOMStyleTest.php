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
        <div style="text-align: left;">First</div>
        <div style="text-align: right;">Second</div>
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
  
  function testCSSRead() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $this->assertEquals('left', $items->css('text-align'));
  }
  
  function testCSSReadWithInvalidProperty() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $this->assertEquals(NULL, $items->css('---'));
  }
  
  function testCSSReadOnEmpty() {
    $items = FluentDOMStyle(self::HTML);
    $this->assertTrue($items instanceof FluentDOMStyle);
    $this->assertEquals(NULL, $items->css('text-align'));
  }
  
  function testCSSWriteWithString() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $items->css('text-align', 'center');
    $this->assertEquals('text-align: center;', $items->eq(0)->attr('style'));
    $this->assertEquals('text-align: center;', $items->eq(1)->attr('style'));
  }
  
  function testCSSWriteWithArray() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $items->css(
      array(
        'text-align' => 'center',
        'color' => 'black'
      )
    );
    $this->assertEquals('color: black; text-align: center;', $items->eq(0)->attr('style'));
    $this->assertEquals('color: black; text-align: center;', $items->eq(1)->attr('style'));
  }
  
  function testCSSWriteWithFunction() {
    $items = FluentDOMStyle(self::HTML)->find('//div');
    $this->assertTrue($items instanceof FluentDOMStyle);
    $items->css('text-align', array($this, 'callbackTestCSSWriteWithFunction'));
    $this->assertEquals('text-align: right;', $items->eq(0)->attr('style'));
    $this->assertEquals('text-align: left;', $items->eq(1)->attr('style'));
  }
  
  function testCSSWriteWithInvalidProperty() {
    try {
      FluentDOMStyle(self::HTML)->find('//div')->css('---', '');
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }
  
  function testCSSWriteWithInvalidPropertyInArray() {
    try {
      FluentDOMStyle(self::HTML)->find('//div')->css(array('---' => ''));
    } catch (InvalidArgumentException $expected) {
      return;
    } catch (Exception $expected) {
      $this->fail('An unexpected exception has been raised: '.$expected->getMessage());
    }
    $this->fail('An expected exception has not been raised.');
  }
  
  /*
  * helper
  */
  function callbackTestCSSWriteWithFunction($node, $property, $value) {
    switch ($value) {
    case 'left' :
      return 'right';
    case 'right' :
      return 'left';
    default :
      return 'center';
    }
  }
}
?>
