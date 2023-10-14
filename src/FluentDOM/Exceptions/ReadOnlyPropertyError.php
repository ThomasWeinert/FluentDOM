<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
namespace FluentDOM\Exceptions {


  use FluentDOM\Exception;

  class ReadOnlyPropertyError extends \Error implements Exception {

    public function __construct(string|object $classOrObject, string $property) {
      parent::__construct(
        \sprintf(
          'Can not write read only property %s::$%s.',
          is_string($classOrObject) ? $classOrObject : $classOrObject::class,
          $property
        )
      );
    }
  }
}

