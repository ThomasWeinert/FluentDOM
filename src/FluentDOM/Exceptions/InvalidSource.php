<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class InvalidSource extends \InvalidArgumentException implements Exception {

    /**
     * @param mixed $source
     * @param string $contentType
     */
    public function __construct($source, $contentType) {
      parent::__construct(
        sprintf(
          'Can not load %s as "%s".',
          (is_object($source) ? get_class($source) : gettype($source)),
          $contentType
        )
      );
    }
  }
}