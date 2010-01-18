<?php
/**
* Collection of test for the FluentDOMStyle class supporting PHP 5.2
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Tests
*/

/**
* load necessary files
*/
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/../../FluentDOM/Style.php');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOMStyle.
*
* @package FluentDOM
* @subpackage Tests
*/
class FluentDOMStyleTest extends PHPUnit_Framework_TestCase {

  const HTML = '
    <html>
      <body>
        <div style="text-align: left;">First</div>
        <div style="text-align: right;">Second</div>
        <div>Third</div>
      </body>
    </html>
  ';

  /**
  * @group Functions
  */
  public function testFunction() {
    $fd = FluentDOMStyle();
    $this->assertTrue($fd instanceof FluentDOMStyle);
  }

  /**
  * @group Functions
  */
  public function testFunctionWithContent() {
    $dom = new DOMDocument();
    $node = $dom->appendChild($dom->createElement('html'));
    $fd = FluentDOMStyle($node);
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $this->assertEquals('html', $fd->document->documentElement->nodeName);
  }

  /**
  * Get FluentDOMStyle instance with loaded html document using a mock loader
  *
  * @access protected
  * @return FluentDOMStyle
  */
  protected function getFixture() {
    $dom = new DOMDocument();
    $dom->loadXML(self::HTML);
    $loader = $this->getMock('FluentDOMLoader');
    $loader->expects($this->once())
           ->method('load')
           ->with($this->equalTo(''))
           ->will($this->returnValue($dom));
    $fd = new FluentDOMStyle();
    $fd->setLoaders(array($loader));
    return $fd->load('');
  }

  /**
  * @group CSS
  */
  public function testChaining() {
    $fd = $this->getFixture();
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $findFd = $fd->find('//div');
    $this->assertTrue($findFd instanceof FluentDOMStyle);
    $this->assertTrue($fd !== $findFd);
  }

  /**
  * @group CSS
  */
  public function testCssRead() {
    $fd = $this->getFixture()->find('//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $this->assertEquals('left', $fd->css('text-align'));
  }

  /**
  * @group CSS
  */
  public function testCssReadWithInvalidProperty() {
    $fd = $this->getFixture()->find('//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $this->assertEquals(NULL, $fd->css('---'));
  }

  /**
  * @group CSS
  */
  public function testCssReadOnEmpty() {
    $fd = $this->getFixture();
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $this->assertEquals(NULL, $fd->css('text-align'));
  }

  /**
  * @group CSS
  */
  public function testCssReadOnTextNodes() {
    $fd = $this->getFixture()->find('//div')->children()->andSelf();
    $this->assertTrue(count($fd) > 3);
    $this->assertEquals('left', $fd->css('text-align'));
  }

  /**
  * @group CSS
  */
  public function testCssWriteWithString() {
    $fd = $this->getFixture()->find('//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $fd->css('text-align', 'center');
    $this->assertEquals('text-align: center;', $fd->eq(0)->attr('style'));
    $this->assertEquals('text-align: center;', $fd->eq(1)->attr('style'));
  }

  /**
  * @group CSS
  */
  public function testCssWriteWithArray() {
    $fd = $this->getFixture()->find('//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $fd->css(
      array(
        'text-align' => 'center',
        'color' => 'black'
      )
    );
    $this->assertEquals('color: black; text-align: center;', $fd->eq(0)->attr('style'));
    $this->assertEquals('color: black; text-align: center;', $fd->eq(1)->attr('style'));
  }

  /**
  * @group CSS
  */
  public function testCssWriteWithFunction() {
    $fd = $this->getFixture()->find('//div');
    $this->assertTrue($fd instanceof FluentDOMStyle);
    $fd->css('text-align', array($this, 'callbackTestCssWriteWithFunction'));
    $this->assertEquals('text-align: right;', $fd->eq(0)->attr('style'));
    $this->assertEquals('text-align: left;', $fd->eq(1)->attr('style'));
  }

  /**
  * @group CSS
  */
  public function testCssWriteWithInvalidProperty() {
    try {
      $this->getFixture()->find('//div')->css('---', '');
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group CSS
  */
  public function testCssWriteWithInvalidPropertyInArray() {
    try {
      $this->getFixture()->find('//div')->css(array('---' => ''));
      $this->fail('An expected exception has not been raised.');
    } catch (InvalidArgumentException $expected) {
    }
  }

  /**
  * @group CSS
  */
  public function testCssRemoveProperty() {
    $fd = $this->getFixture()->find('//div');
    $fd->css('text-align', '');
    $this->assertFalse($fd[0]->hasAttribute('style'));
  }

  /**
  * @group CSS
  */
  public function testCssRemoveProperties() {
    $fd = $this->getFixture()->find('//div');
    $fd->css(
      array(
        'text-align' => '',
        'font-weight' => ''
      )
    );
    $this->assertFalse($fd[0]->hasAttribute('style'));
  }

  /**
  * @group CSS
  */
  public function testCssSortPropertiesName() {
    $fd = $this->getFixture()->find('//div');
    $fd->css(
      array(
       'padding' => '0em',
       'margin' => '1em'
      )
    );
    $expect = 'margin: 1em; padding: 0em;';
    $this->assertEquals($expect, $fd[2]->getAttribute('style'));
  }

  /**
  * @group CSS
  */
  public function testCssSortPropertiesLevels() {
    $fd = $this->getFixture()->find('//div');
    $fd->css(
      array(
       'border' => '1px solid red',
       'border-top-color' => 'black',
       'border-top' => '2px solid blue'
      )
    );
    $expect = 'border: 1px solid red; border-top: 2px solid blue; border-top-color: black;';
    $this->assertEquals($expect, $fd[2]->getAttribute('style'));
  }

  /**
  * @group CSS
  */
  public function testCssSortPropertiesPrefix() {
    $fd = $this->getFixture()->find('//div');
    $fd->css(
      array(
       '-moz-opacity' => 30,
       '-o-opacity' => 30,
       'opacity' => 30
      )
    );
    $expect = 'opacity: 30; -moz-opacity: 30; -o-opacity: 30;';
    $this->assertEquals($expect, $fd[2]->getAttribute('style'));
  }

  /*
  * helper
  */
  public function callbackTestCssWriteWithFunction($node, $property, $value) {
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