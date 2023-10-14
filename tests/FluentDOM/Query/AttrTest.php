<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class AttrTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getFirstElement
     */
    public function testAttrRead(): void {
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
    public function testAttrReadFromRoot(): void {
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
    public function testAttrReadInvalid(): void {
      $this->expectException(\UnexpectedValueException::class);
      $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->attr('');
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     */
    public function testAttrReadNoMatch(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->attr('index');
      $this->assertNull($fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getFirstElement
     */
    public function testAttrReadNoAttribute(): void {
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
    public function testAttrReadOnDOMText(): void {
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
    public function testAttrWrite(): void {
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
    public function testAttrWriteWithNullValue(): void {
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
    public function testAttrWriteWithInvalidNames($attrName): void {
      $this->expectException(\UnexpectedValueException::class);
      $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->attr($attrName, '');
    }

    public static function dataProviderInvalidAttributeNames(): array {
      return [
        ['1foo'],
        ['1bar:foo'],
        ['bar:1foo'],
        ['bar:foo<>'],
        ['bar:'],
        [':foo']
      ];
    }

    /**
     * @group Attributes
     * @dataProvider dataProviderValidAttributeNames
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWriteWithValidNames($attrName): void {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//item')
        ->attr($attrName, 'foo');
      $this->assertTrue($fd->item(0)->hasAttribute($attrName));
      $this->assertEquals('foo', $fd->item(0)->getAttribute($attrName));
    }

    public static function dataProviderValidAttributeNames(): array {
      return [
        ['foo'],
        ['bar:foo']
      ];
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWriteWithArray(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)
        ->find('//group/item')
        ->attr(['index' => '15', 'length' => '34', 'label' => 'box']);
      $this->assertEquals('15', $fd->attr('index'));
      $this->assertEquals('34', $fd->attr('length'));
      $this->assertEquals('box', $fd->attr('label'));
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::attr
     * @covers \FluentDOM\Query::getSetterValues
     */
    public function testAttrWriteWithCallback(): void {
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
    public function testHasAttrExpectingTrue(): void {
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
    public function testHasAttrNotOnFirstNodeExpectingTrue(): void {
      $this->assertTrue(
        $this->getQueryFixtureFromString(self::XML)
          ->find('//group/item')
          ->find('//group')
          ->addBack()
          ->hasAttr('index')
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::hasAttr
     */
    public function testHasAttrExpectingFalse(): void {
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
    public function testRemoveAttr(): void {
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
    public function testRemoveAttrWithInvalidParameter(): void {
      $fd = new Query();
      $this->expectException(\Throwable::class);
      $fd->removeAttr(1);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::removeAttr
     * @covers \FluentDOM\Query::getNamesList
     */
    public function testRemoveAttrWithListParameter(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->removeAttr(['index', 'style']);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::removeAttr
     * @covers \FluentDOM\Query::getNamesList
     */
    public function testRemoveAttrWithAsteriskParameter(): void {
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
    public function testPropertyAttrGet(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item[2]');
      $attr = $fd->attr;
      $this->assertInstanceOf(Attributes::class, $attr);
      $this->assertSame(
        $fd, $attr->getOwner()
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::__set
     */
    public function testPropertyAttrSetWithArray(): void {
      $fd = $this->getQueryFixtureFromString('<sample/>')->find('/*');
      $fd->attr = [
        'foo' => 1,
        'bar' => 2
      ];
      $this->assertEquals(
        '<sample foo="1" bar="2"/>', $fd->document->saveXML($fd[0])
      );
    }

    /**
     * @group Attributes
     * @covers \FluentDOM\Query::__set
     */
    public function testPropertyAttrSetWithFluentDOMAttributes(): void {
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
