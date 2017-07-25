<?php

namespace FluentDOM\Exceptions\InvalidSource {

  use FluentDOM\Exceptions;

  class Variable extends \InvalidArgumentException implements Exceptions\InvalidSource {

    /**
     * @param mixed $source
     * @param string $contentType
     */
    public function __construct($source, string $contentType) {
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