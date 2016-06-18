<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class InvalidArgument extends \InvalidArgumentException implements Exception {

    /**
     * @param string $argumentName
     * @param string|string[] $expectedTypes
     */
    public function __construct() {
      $message = sprintf('Loader can not load fragments.');
      parent::__construct($message);
    }
  }
}