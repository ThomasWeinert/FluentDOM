<?php
namespace FluentDOM\Serializer {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../TestCase.php');

  class JsonTest extends TestCase {

    /**
     * @covers FluentDOM\Serializer\Json
     * @dataProvider provideExamples
     * @param string $expected
     * @param string $data
     * @param int $options
     * @param int $depth
     */
    public function testToString(
      $expected, $data, $options = 0, $depth = 256
    ) {
      $serializer = new Json_TestProxy(new \DOMDocument(), $options, $depth);
      $serializer->jsonData = $data;
      $this->assertEquals(
        $expected, (string)$serializer
      );
    }

    /**
     * @covers FluentDOM\Serializer\Json
     */
    public function testToStringWithLimitedDepthExpectingEmptyString() {
      if (version_compare(PHP_VERSION, '5.5.0', '<') || defined('HHVM_VERSION')) {
        $this->markTestSkipped('Minimum version for $depth argument is PHP 5.5');
      }
      $serializer = new Json_TestProxy(new \DOMDocument(), 0, 1);
      $serializer->jsonData = self::getArrayAsStdClass(
        [
          'alice' => 'bob',
          'depth' => self::getArrayAsStdClass(
            [
              'charlie' => 'david'
            ]
          )
        ]
      );
      $this->assertEquals('', (string)$serializer);
    }


    /**
     * @covers FluentDOM\Serializer\Json
     * @dataProvider provideExamples
     * @param string $expected
     * @param string $data
     * @param int $options
     * @param int $depth
     */
    public function testJsonSerializable(
      $expected, $data, $options = 0, $depth = 256
    ) {
      $serializer = new Json_TestProxy(new \DOMDocument());
      $serializer->jsonData = $data;
      $json = version_compare(PHP_VERSION, '5.5.0', '>=')
        ? json_encode($serializer, $options, $depth)
        : json_encode($serializer, $options);
      $this->assertEquals($expected, $json);
    }

    /**
     * @covers FluentDOM\Serializer\Json
     */
    public function testJsonSerializableWithLimitedDepthExpectingFalse() {
      if (version_compare(PHP_VERSION, '5.5.0', '<') || defined('HHVM_VERSION')) {
        $this->markTestSkipped('Minimum version for $depth argument is PHP 5.5');
      }
      $serializer = new Json_TestProxy(new \DOMDocument());
      $serializer->jsonData = self::getArrayAsStdClass(
        [
          'alice' => 'bob',
          'depth' => self::getArrayAsStdClass(
            [
              'charlie' => 'david'
            ]
          )
        ]
      );
      $this->assertFalse(
        json_encode($serializer, 0, 1)
      );
    }

    /**
     * @covers FluentDOM\Serializer\Json
     */
    public function testJsonSerializeCallingGetNode() {
      $dom = new Document();
      $dom->appendElement('success');
      $serializer = new Json_TestProxy($dom);
      $this->assertEquals(
        '["success"]', json_encode($serializer)
      );
    }

    /**
     * @covers FluentDOM\Serializer\Json
     */
    public function testJsonSerializeCallingGetEmpty() {
      $serializer = new Json_TestProxy(new \DOMDocument());
      $this->assertEquals(
        '{}', json_encode($serializer)
      );
    }

    public static function provideExamples() {
      return [
        [
          '{"alice":"bob"}',
          self::getArrayAsStdClass(['alice' => 'bob'])
        ],
        [
          "{\n    \"alice\": \"bob\"\n}",
          self::getArrayAsStdClass(['alice' => 'bob']),
          JSON_PRETTY_PRINT
        ]
      ];
    }

    public static function getArrayAsStdClass($properties) {
      $data = new \stdClass();
      foreach ($properties as $name => $value) {
        $data->{$name} = $value;
      }
      return $data;
    }
  }

  class Json_TestProxy extends Json {
    public $jsonData = NULL;

    public function jsonSerialize() {
      return $this->jsonData ?: parent::jsonSerialize();
    }

    public function getNode(\DOMElement $node) {
      return [ $node->nodeName ];
    }
  }
}