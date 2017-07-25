<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class InvalidSerializer extends \UnexpectedValueException implements Exception {

    public function __construct(string $contentType, string $class) {
      parent::__construct(
        sprintf(
          'Invalid serializer for content type %s, instances of %s are not castable to string.',
          $contentType,
          $class
        )
      );
    }
  }
}