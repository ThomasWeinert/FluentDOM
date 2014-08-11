<?php
namespace FluentDOM\Serializer {

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
      $this->assertEquals(
        $expected, json_encode($serializer, $options, $depth)
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
        ],
        [
          FALSE,
          self::getArrayAsStdClass(
            [
              'alice' => 'bob',
              'depth' => self::getArrayAsStdClass(
                [
                  'charlie' => 'david'
                ]
              )
            ]
          ),
          0,
          1
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
    public $jsonData = [];

    public function jsonSerialize() {
      return $this->jsonData;
    }
  }
}