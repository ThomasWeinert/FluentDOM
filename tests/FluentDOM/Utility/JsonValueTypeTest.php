<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Utility {

  use FluentDOM\TestCase;

  require_once __DIR__ . '/../TestCase.php';

  /**
   * @covers \FluentDOM\Utility\JsonValueType
   */
  class JsonValueTypeTest extends TestCase {

    /**
     * @dataProvider provideValuesAndTypes
     */
    public function testGetTypeFromValue(string $expectedType, mixed $value): void {
      $this->assertSame(
        $expectedType, JsonValueType::getTypeFromValue($value)
      );
    }

    public static function provideValuesAndTypes(): array {
      return [
        'number' => [JsonValueType::TYPE_NUMBER, 42],
        'boolean' => [JsonValueType::TYPE_BOOLEAN, true],
        'null' => [JsonValueType::TYPE_NULL, NULL],
        'string' => [JsonValueType::TYPE_STRING, 'test'],
        'numerical array' => [JsonValueType::TYPE_ARRAY, [42]],
        'associative array' => [JsonValueType::TYPE_OBJECT, ['foo' => 42]],
        'object' => [JsonValueType::TYPE_OBJECT, new \stdClass()],
      ];
    }
  }
}
