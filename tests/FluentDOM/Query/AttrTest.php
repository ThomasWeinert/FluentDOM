<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class AttrTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getFirstElement
     */
    public function testAttrRead() {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//group/item')
        ->attr('index');
      $this->assertEquals('0', $fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getFirstElement
     */
    public function testAttrReadFromRoot() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $this->assertEquals('1.0', $fd->find('/*')->attr('version'));
      $this->assertEquals('1.0', $fd->find('/items')->attr('version'));
      $this->assertEquals('1.0', $fd->find('//items')->attr('version'));
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getFirstElement
     */
    public function testAttrReadInvalid() {
      $this->setExpectedException(\UnexpectedValueException::class);
      $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->attr('');
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     */
    public function testAttrReadNoMatch() {
      $fd = $this->getQueryFixtureFromString(self::XML)->attr('index');
      $this->assertNull($fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getFirstElement
     */
    public function testAttrReadNoAttribute() {
      $fd = $this
        ->getQueryFixtureFromString(self::XML)
        ->find('//group')
        ->attr('index');
      $this->assertNull($fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getFirstElement
     */
    public function testAttrReadOnDomtext() {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//item/text()')
        ->attr('index');
      $this->assertTrue(empty($fd));
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWrite() {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//group/item')
        ->attr('index', '15')
        ->attr('index');
      $this->assertEquals('15', $fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWriteWithNullValue() {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//group/item')
        ->attr('index', NULL)
        ->attr('index');
      $this->assertEquals('', $fd);
    }


    /**
     * @group Attributes
     * @dataProvider dataProviderInvalidAttributeNames
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWriteWithInvalidNames($attrName) {
      try {
        $this->getQueryFixtureFromString(self::XML)
          ->find('//item')
          ->attr($attrName, '');
        $this->fail('An expected exception has not been raised.');
      } catch (\UnexpectedValueException $expected) {
      }
    }

    public static function dataProviderInvalidAttributeNames() {
      return array(
        array('1foo'),
        array('1bar:foo'),
        array('bar:1foo'),
        array('bar:foo<>'),
        array('bar:'),
        array(':foo')
      );
    }

    /**
     * @group Attributes
     * @dataProvider dataProviderValidAttributeNames
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWriteWithValidNames($attrName) {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->attr($attrName, 'foo');
      $this->assertTrue($fd->item(0)->hasAttribute($attrName));
      $this->assertEquals('foo', $fd->item(0)->getAttribute($attrName));
    }

    public static function dataProviderValidAttributeNames() {
      return array(
        array('foo'),
        array('bar:foo')
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWriteWithArray() {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//group/item')
        ->attr(array('index' => '15', 'length' => '34', 'label' => 'box'));
      $this->assertEquals('15', $fd->attr('index'));
      $this->assertEquals('34', $fd->attr('length'));
      $this->assertEquals('box', $fd->attr('label'));
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWriteWithCallback() {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//group/item')
        ->attr(
          'index',
          function($node, $index, $content) {
            return 'Callback #'.$content;
          }
        );
      $this->assertEquals('Callback #0', $fd->attr('index'));
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::hasAttr
     */
    public function testHasAttrExpectingTrue() {
      $this->assertTrue(
        $this->getQueryFixtureFromString(self::XML)
          ->find('//group/item')
          ->hasAttr('index')
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::hasAttr
     */
    public function testHasAttrNotOnFirstNodeExpectingTrue() {
      $this->assertTrue(
        $this->getQueryFixtureFromString(self::XML)
          ->find('//group/item')
          ->find('//group')
          ->andSelf()
          ->hasAttr('index')
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::hasAttr
     */
    public function testHasAttrExpectingFalse() {
      $this->assertFalse(
        $this->getQueryFixtureFromString(self::XML)
          ->find('//group')
          ->hasAttr('index')
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::removeAttr
     * @covers \FluentDOM\Query::getNamesList
     */
    public function testRemoveAttr() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->removeAttr('index');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::removeAttr
     * @covers \FluentDOM\Query::getNamesList
     */
    public function testRemoveAttrWithInvalidParameter() {
      $fd = new Query();
      $this->setExpectedException(\InvalidArgumentException::class);
      $fd->removeAttr(1);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::removeAttr
     * @covers \FluentDOM\Query::getNamesList
     */
    public function testRemoveAttrWithListParameter() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->removeAttr(array('index', 'style'));
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::removeAttr
     * @covers \FluentDOM\Query::getNamesList
     */
    public function testRemoveAttrWithAsteriskParameter() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->removeAttr('*');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::__get
     */
    public function testPropertyAttrGet() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item[2]');
      $attr = $fd->attr;
      $this->assertInstanceOf(Attributes::class, $attr);
      $this->assertAttributeSame(
        $fd, '_fd', $attr
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::__set
     */
    public function testPropertyAttrSetWithArray() {
      $fd = $this->getQueryFixtureFromString('<sample/>')->find('/*');
      $fd->attr = array(
        'foo' => 1,
        'bar' => 2
      );
      $this->assertEquals(
        '<sample foo="1" bar="2"/>', $fd->document->saveXml($fd[0])
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::__set
     */
    public function testPropertyAttrSetWithFluentDOMAttributes() {
      $fd = $this->getQueryFixtureFromString('<sample><item foo="1"/><item/></sample>')->find('//item');
      $buffer = $fd->attr;
      $fd->attr = $buffer;
      $this->assertEquals(
        '<sample><item foo="1"/><item foo="1"/></sample>',
        $fd->document->saveXml($fd->document->documentElement)
      );
    }
  }
}