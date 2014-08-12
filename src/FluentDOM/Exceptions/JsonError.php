<?php

namespace FluentDOM\Exceptions {

  class JsonError extends \UnexpectedValueException {

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

    public function __construct($code) {
      parent::__construct(
        isset($this->_jsonErrors[$code]) ? $this->_jsonErrors[$code] : $this->_jsonErrors[-1],
        $code
      );
    }

  }
}