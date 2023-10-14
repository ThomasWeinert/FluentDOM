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

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class UndeclaredPropertyError extends \Error implements Exception {

    public function __construct(string|object $classOrObject, string $property) {
      parent::__construct(
        \sprintf(
          'Undeclared property %s::$%s not available.',
          is_string($classOrObject) ? $classOrObject : $classOrObject::class,
          $property
        )
      );
    }
  }
}
