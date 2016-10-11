<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class NoSerializer extends \UnexpectedValueException implements Exception {

    public function __construct($contentType) {
      parent::__construct(
        sprintf(
          'No serializer for content type %s available.',
          $contentType
        )
      );
    }
  }
}