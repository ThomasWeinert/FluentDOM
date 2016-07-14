<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class InvalidFragmentLoader extends \InvalidArgumentException implements Exception {

    /**
     * @param string $className
     */
    public function __construct($className) {
      $message = sprintf('Loader "%s" can not load fragments.');
      parent::__construct($message);
    }
  }
}