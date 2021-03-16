<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader\Libxml {

  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\Exceptions\LoadingError\SourceNotLoaded;
  use FluentDOM\Loader\Result as LoaderResult;

  class Errors {

    public const ERROR_NONE = 0;
    public const ERROR_WARNING = 1;
    public const ERROR_ERROR = 2;
    public const ERROR_FATAL = 4;
    public const ERROR_ALL = self::ERROR_WARNING | self::ERROR_ERROR | self::ERROR_FATAL;

    private $_errorMapping = [
      LIBXML_ERR_NONE => self::ERROR_NONE,
      LIBXML_ERR_WARNING => self::ERROR_WARNING,
      LIBXML_ERR_ERROR => self::ERROR_ERROR,
      LIBXML_ERR_FATAL => self::ERROR_FATAL
    ];

    public function capture(callable $callback, int $errorLevel = self::ERROR_FATAL) {
      $exception = NULL;
      $result = NULL;
      $errorSetting = \libxml_use_internal_errors(TRUE);
      \libxml_clear_errors();
      try {
        $result = $callback();
      } catch (\Throwable $exception) {
      }
      if ($exception || $errorLevel !== self::ERROR_NONE) {
        foreach (\libxml_get_errors() as $error) {
          $severity = $this->_errorMapping[$error->level];
          if (($errorLevel & $severity) === $severity) {
            $exception = new LoadingError\Libxml($error);
            break;
          }
        }
      }
      \libxml_clear_errors();
      \libxml_use_internal_errors($errorSetting);
      if ($exception instanceof \Throwable) {
        throw $exception;
      }
      if (
        $result instanceof \DOMDocument ||
        $result instanceof \DOMDocumentFragment ||
        $result instanceof LoaderResult
      ) {
        return $result;
      }
      throw new SourceNotLoaded();
    }
  }
}
