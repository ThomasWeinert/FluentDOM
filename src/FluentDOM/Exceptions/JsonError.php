<?php

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class JsonError extends \UnexpectedValueException implements Exception {

    /**
     * JSON errors
     * @var array $_jsonErrors
     */
    private $_jsonErrors = array(
      -1 => 'Unknown error has occurred',
      0 => 'No error has occurred',
      1 => 'The maximum stack depth has been exceeded',
      3 => 'Control character error, possibly incorrectly encoded',
      4 => 'Syntax error',
    );

    /**
     * @param int $code
     */
    public function __construct($code) {
      parent::__construct(
        isset($this->_jsonErrors[$code]) ? $this->_jsonErrors[$code] : $this->_jsonErrors[-1],
        $code
      );
    }

  }
}