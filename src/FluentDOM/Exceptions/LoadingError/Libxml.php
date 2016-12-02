<?php
namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\Exceptions;

  class Libxml extends \UnexpectedValueException implements Exceptions\LoadingError {

    public function __construct($error) {
      var_dump($error);
    }
  }
}