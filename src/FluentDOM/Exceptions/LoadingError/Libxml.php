<?php
namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\Exceptions;

  class Libxml extends \UnexpectedValueException implements Exceptions\LoadingError {

    private static $_levels = [
      LIBXML_ERR_WARNING => 'warning',
      LIBXML_ERR_ERROR => 'error',
      LIBXML_ERR_FATAL => 'fatal error'
    ];

    public function __construct(\LibXMLError $error) {
      $message = 'Libxml %1$s in %2$s line %3$d at character %4$d: %5$s.';
      if (empty($error->file)) {
        $message = 'Libxml %1$s in line %3$d at character %4$d: %5$s.';
      }
      parent::__construct(
        sprintf(
          $message,
          self::$_levels[$error->level],
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