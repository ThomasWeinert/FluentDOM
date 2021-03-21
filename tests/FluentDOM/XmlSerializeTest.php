<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  use FluentDOM\DOM\Element;

  require_once __DIR__.'/TestCase.php';

  abstract class XmlSerialize_TestProxy implements XmlSerializable {

    use XmlSerialize;

  }

  abstract class XmlSerialize_TestProxyInvalid  {

    use XmlSerialize;

  }

  class XmlSerializeTest extends TestCase {

    /**
     * @covers \FluentDOM\XmlSerialize
     */
    public function testGetXmlWithOneElement(): void {
      $object = $this->getMockForAbstractClass('FluentDOM\\XmlSerialize_TestProxy');
      $object
        ->expects($this->once())
        ->method('appendTo')
        ->will(
          $this->returnCallback(
            function (Element $parent) {
              $parent->appendElement('test');
            }
          )
        );
      $this->assertXmlStringEqualsXmlString('<test/>', $object->getXml());
    }

    /**
     * @covers \FluentDOM\XmlSerialize
     */
    public function testGetXmlWithoutInterfaceExpectingException(): void {
      $object = $this->getMockBuilder(XmlSerialize_TestProxyInvalid::class)->getMockForAbstractClass();
      $this->expectException(\LogicException::class);
      $object->getXml();
    }
  }
}
