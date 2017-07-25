<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class InvalidFragmentLoader extends \InvalidArgumentException implements Exception {

    /**
     * @param string $className
     */
    public function __construct(string $className) {
      parent::__construct(sprintf('Loader "%s" can not load fragments.', $className));
    }
  }
}