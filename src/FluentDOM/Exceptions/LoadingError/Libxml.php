<?php
namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\Exceptions;

  class Libxml extends \UnexpectedValueException implements Exceptions\LoadingError {

    private $_levels = [
      LIBXML_ERR_WARNING => "warning",
      LIBXML_ERR_ERROR => "error",
      LIBXML_ERR_FATAL => "fatal error"
    ];

    public function __construct($error) {
      if (empty($error->file)) {
        $message = 'Libxml %1$s in line %3$d at character %4$d: %5$s.';
      } else {
        $message = 'Libxml %1$s in %2$s line %3$d at character %4$d: %5$s.';
      }
      parent::__construct(
        sprintf(
          $message,
          $this->_levels[$error->level],
          $error->file,
          $error->line,
          $error->column,
          trim($error->message)
        ),
        $error->code
      );
    }
  }
}