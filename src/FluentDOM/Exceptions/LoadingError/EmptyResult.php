<?php

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\Exceptions;

  class EmptyResult extends \UnexpectedValueException implements Exceptions\LoadingError {

    public function __construct() {
      parent::__construct(
        'Parsing result did not contain an usable node.'
      );
    }

  }
}