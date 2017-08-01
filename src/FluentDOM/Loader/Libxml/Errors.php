<?php

namespace FluentDOM\Loader\Libxml {

  use FluentDOM\Exceptions\LoadingError;

  class Errors {

    const ERROR_NONE = 0;
    const ERROR_WARNING = 1;
    const ERROR_ERROR = 2;
    const ERROR_FATAL = 4;
    const ERROR_ALL = self::ERROR_WARNING | self::ERROR_ERROR | self::ERROR_FATAL;

    private $_errorMapping = [
      LIBXML_ERR_NONE => self::ERROR_NONE,
      LIBXML_ERR_WARNING => self::ERROR_WARNING,
      LIBXML_ERR_ERROR => self::ERROR_ERROR,
      LIBXML_ERR_FATAL => self::ERROR_FATAL
    ];

    public function capture(callable $callback, int $errorLevel = self::ERROR_FATAL) {
      $exception = FALSE;
      $errorSetting = libxml_use_internal_errors(TRUE);
      libxml_clear_errors();
      $result = $callback();
      if ($errorLevel !== self::ERROR_NONE) {
        foreach (libxml_get_errors() as $error) {
          $severity = $this->_errorMapping[$error->level];
          if (($errorLevel & $severity) === $severity) {
            $exception = new LoadingError\Libxml($error);
            break;
          }
        }
      }
      libxml_clear_errors();
      libxml_use_internal_errors($errorSetting);
      if ($exception instanceof LoadingError\Libxml) {
        throw $exception;
      }
      return $result;
    }
  }
}