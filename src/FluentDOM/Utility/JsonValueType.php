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

namespace FluentDOM\Utility {

  abstract class JsonValueType {

    public const TYPE_NULL = 'null';
    public const TYPE_STRING = 'string';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_NUMBER = 'number';
    public const TYPE_OBJECT = 'object';
    public const TYPE_ARRAY = 'array';


    /**
     * Get the type from a variable value.
     */
    public static function getTypeFromValue(mixed $value): string {
      if (\is_array($value)) {
        if (empty($value) || \array_keys($value) === \range(0, \count($value) - 1)) {
          return self::TYPE_ARRAY;
        }
        return self::TYPE_OBJECT;
      }
      if (\is_object($value)) {
        return self::TYPE_OBJECT;
      }
      if (NULL === $value) {
        return self::TYPE_NULL;
      }
      if (\is_bool($value)) {
        return self::TYPE_BOOLEAN;
      }
      if (\is_int($value) || \is_float($value)) {
        return self::TYPE_NUMBER;
      }
      return self::TYPE_STRING;
    }
  }
}
