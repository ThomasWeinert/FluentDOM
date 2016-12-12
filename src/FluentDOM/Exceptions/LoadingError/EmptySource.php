<?php

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\Exceptions;

  class EmptySource extends \UnexpectedValueException implements Exceptions\LoadingError {

    public function __construct() {
      parent::__construct(
        'Given source was empty.'
      );
    }

  }
}