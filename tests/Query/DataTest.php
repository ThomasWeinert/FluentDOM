<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class DataTest extends TestCase {

    protected $_directory = __DIR__;
    /**
     * @covers FluentDOM\Data::__construct
     */
    public function testConstructor() {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('sample'));
      $data = new Data($dom->documentElement);
      $this->assertAttributeSame(
        $dom->documentElement, '_node', $data
      );
    }

    /**
     * @covers FluentDOM\Data::toArray
     * @covers FluentDOM\Data::isDataProperty
     */
    public function testToArrayWithSeveralAttributes() {
      $dom = new \DOMDocument();
      $dom->loadXML(
        '<div data-role="page" data-hidden="true" data-options=\'{"name":"John"}\'></div>'
      );
      $options = new \stdClass();
      $options->name = 'John';
      $data = new Data($dom->documentElement);
      $this->assertEquals(
        array(
          'role' => 'page',
          'hidden' => TRUE,
          'options' => $options
        ),
        $data->toArray()
      );
    }

    /**
     * @covers FluentDOM\Data::toArray
     * @covers FluentDOM\Data::decodeName
     */
    public function testToArrayWithComplexAttribute() {
      $dom = new \DOMDocument();
      $dom->loadXML(
        '<div data-options-name="John"></div>'
      );
      $data = new Data($dom->documentElement);
      $this->assertEquals(
        array(
          'optionsName' => 'John'
        ),
        $data->toArray()
      );
    }

    /**
     * @covers FluentDOM\Data::getIterator
     */
    public function testGetIterator() {
      $dom = new \DOMDocument();
      $dom->loadXML(
        '<div data-role="page" data-hidden="true"></div>'
      );
      $data = new Data($dom->documentElement);
      $this->assertEquals(
        array(
          'role' => 'page',
          'hidden' => TRUE
        ),
        iterator_to_array($data)
      );
    }

    /**
     * @covers FluentDOM\Data::count
     */
    public function testCountExpectingZero() {
      $dom = new \DOMDocument();
      $dom->loadXML(
        '<div></div>'
      );
      $data = new Data($dom->documentElement);
      $this->assertEquals(
        0, count($data)
      );
    }

    /**
     * @covers FluentDOM\Data::count
     */
    public function testCountExpectingTwo() {
      $dom = new \DOMDocument();
      $dom->loadXML(
        '<div data-role="page" data-hidden="true"></div>'
      );
      $data = new Data($dom->documentElement);
      $this->assertEquals(
        2, count($data)
      );
    }

    /**
     * @covers FluentDOM\Data::__isset
     */
    public function testMagicMethodIssetExpectingTrue() {
      $dom = new \DOMDocument();
      $dom->loadXML('<node data-truth="true"/>');
      $data = new Data($dom->documentElement);
      $this->assertTrue(isset($data->truth));
    }

    /**
     * @covers FluentDOM\Data::__isset
     */
    public function testMagicMethodIssetExpectingFalse() {
      $dom = new \DOMDocument();
      $dom->loadXML('<node data-truth="true"/>');
      $data = new Data($dom->documentElement);
      $this->assertFalse(isset($data->lie));
    }

    /**
     * @covers FluentDOM\Data::__get
     * @covers FluentDOM\Data::encodeName
     * @covers FluentDOM\Data::decodeValue
     * @dataProvider provideDataAttributes
     */
    public function testMagicMethodGet($expected, $name, $xml) {
      $dom = new \DOMDocument();
      $dom->loadXML($xml);
      $data = new Data($dom->documentElement);
      $this->assertEquals(
        $expected,
        $data->$name
      );
    }

    /**
     * @covers FluentDOM\Data::__set
     * @covers FluentDOM\Data::encodeName
     * @covers FluentDOM\Data::encodeValue
     * @dataProvider provideDataValues
     */
    public function testMagicMethodSet($expectedXml, $name, $value) {
      $dom = new \DOMDocument();
      $dom->appendChild($dom->createElement('node'));
      $data = new Data($dom->documentElement);
      $data->$name = $value;
      $this->assertEquals(
        $expectedXml, $dom->saveXml($dom->documentElement)
      );
    }

    /**
     * @covers FluentDOM\Data::__unset
     * @covers FluentDOM\Data::encodeName
     * @dataProvider provideDataValues
     */
    public function testMagicMethodUnset() {
      $dom = new \DOMDocument();
      $dom->loadXml('<node data-truth="true"/>');
      $data = new Data($dom->documentElement);
      unset($data->truth);
      $this->assertEquals(
        '<node/>', $dom->saveXml($dom->documentElement)
      );
    }

    public static function provideDataAttributes() {
      return array(
        'string' => array('World', 'Hello', '<node data-hello="World"/>'),
        'boolean true' => array(TRUE, 'truth', '<node data-truth="true"/>'),
        'boolean false' => array(FALSE, 'lie', '<node data-lie="false"/>'),
        'array' => array(
          array('1', '2'),
          'list',
          '<node data-list="[&quot;1&quot;,&quot;2&quot;]"/>'
        ),
        'object' => array(
          self::createObjectFromArray(array('foo' => 'bar')),
          'object',
          '<node data-object="{&quot;foo&quot;:&quot;bar&quot;}"/>'
        ),
        'invalid object' => array(NULL, 'object', '<node data-object=\'{{"foo":"bar"}\'/>'),
        'invalid attrbute' => array(NULL, 'unknown', '<node/>'),
        'complex name' => array(1, 'complexName', '<node data-complex-name="1"/>'),
        'abbreviation' => array(1, 'someABBRName', '<node data-some-abbr-name="1"/>')
      );
    }

    public static function provideDataValues() {
      return array(
        'string' => array('<node data-hello="World"/>', 'hello', 'World'),
        'boolean true' => array('<node data-truth="true"/>', 'truth', TRUE),
        'boolean false' => array('<node data-lie="false"/>', 'lie', FALSE),
        'array' => array('<node data-list="[&quot;1&quot;,&quot;2&quot;]"/>', 'list', array('1', '2')),
        'object' => array(
          '<node data-object="{&quot;foo&quot;:&quot;bar&quot;}"/>',
          'object',
          self::createObjectFromArray(array('foo' => 'bar'))
        ),
        'complex name' => array('<node data-say-hello="World"/>', 'sayHello', 'World'),
        'abbreviation' => array('<node data-some-abbr-name="1"/>', 'someABBRName', 1),
      );
    }

    public static function createObjectFromArray($array) {
      $result = new \stdClass();
      foreach ($array as $key => $value) {
        $result->$key = $value;
      }
      return $result;
    }

    /*****************************
     * Method interface on Query
     *****************************/

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::data
     */
    public function testDataRead() {
      $fd = $this->getQueryFixtureFromString('<sample data-foo="bar"/>')->find('//sample');
      $this->assertEquals('bar', $fd->data('foo'));
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::data
     */
    public function testDataReadWithoutElement() {
      $fd = $this->getQueryFixtureFromString('<sample/>');
      $this->assertEquals('', $fd->data('dummy'));
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::data
     */
    public function testDataWrite() {
      $fd = $this->getQueryFixtureFromString('<sample/>')->find('//sample');
      $fd->data('foo', 'bar');
      $this->assertEquals(
        '<sample data-foo="bar"/>',
        $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::removeData
     */
    public function testRemoveData() {
      $fd = $this->getQueryFixtureFromString(
        '<sample data-foo="bar" data-bar="foo"/>'
      )->find('//sample');
      $fd->removeData('foo');
      $this->assertEquals(
        '<sample data-bar="foo"/>',
        $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::removeData
     */
    public function testRemoveDataWithList() {
      $fd = $this->getQueryFixtureFromString(
        '<sample data-foo="bar" data-bar="foo"/>'
      )->find('//sample');
      $fd->removeData(array('foo', 'bar'));
      $this->assertEquals(
        '<sample/>',
        $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::removeData
     */
    public function testRemoveDataWithoutNamesRemovingAll() {
      $fd = $this->getQueryFixtureFromString(
        '<sample data-foo="bar" data-bar="foo"/>'
      )->find('//sample');
      $fd->removeData();
      $this->assertEquals(
        '<sample/>',
        $fd->document->saveXml($fd->document->documentElement)
      );
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::removeData
     */
    public function testRemoveDataWithInvalidName() {
      $fd = $this->getQueryFixtureFromString(
        '<sample data-foo="bar" data-bar="foo"/>'
      )->find('//sample');
      $this->setExpectedException('InvalidArgumentException');
      $fd->removeData('');
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::hasData
     */
    public function testHasDataExpectingTrue() {
      $fd = $this->getQueryFixtureFromString('<sample data-foo="bar"/>')->find('//sample');
      $this->assertTrue($fd->hasData());
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::hasData
     */
    public function testHasDataExpectingFalse() {
      $fd = $this->getQueryFixtureFromString('<sample/>')->find('//sample');
      $this->assertFalse($fd->hasData());
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::hasData
     */
    public function testHasDataOnEmptyFluentDomExpectingFalse() {
      $fd = $this->getQueryFixtureFromString('<sample/>');
      $this->assertFalse($fd->hasData());
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::hasData
     */
    public function testHasDataOnElementExpectingTrue() {
      $fd = $this->getQueryFixtureFromString('<sample data-foo="bar"/>');
      $this->assertTrue($fd->hasData($fd->document->documentElement));
    }

    /**
     * @group AttributesData
     * @covers FluentDOM\Query::hasData
     */
    public function testHasDataOnElementExpectingFalse() {
      $fd = $this->getQueryFixtureFromString('<sample/>');
      $this->assertFalse($fd->hasData($fd->document->documentElement));
    }
  }
}