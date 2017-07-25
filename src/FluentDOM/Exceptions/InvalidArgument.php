<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class InvalidArgument extends \InvalidArgumentException implements Exception {

    /**
     * @param string $argumentName
     * @param string|string[] $expectedTypes
     */
    public function __construct(string $argumentName, $expectedTypes = NULL) {
      $message = sprintf('Invalid $%s argument.', $argumentName);
      if (is_array($expectedTypes) && count($expectedTypes) > 0) {
        $message .= ' Expected: '.implode(', ', $expectedTypes);
      } elseif (NULL !== $expectedTypes) {
        $message .= ' Expected: '.$expectedTypes;
      }
      parent::__construct($message);
    }
  }
}