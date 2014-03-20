<?php

abstract class FluentDOM {

  private static $_load = TRUE;

  /**
   * Create an FluentDOM::Query instance and load the source into it.
   *
   * @param mixed $source
   * @param string $contentType
   * @return \FluentDOM\Query
   */
  public static function Query($source = NULL, $contentType = 'text/xml') {
    if (self::$_load && !class_exists('FluentDOM\Query')) {
      // @codeCoverageIgnoreStart
      include(__DIR__.'/_require.php');
    }
    // @codeCoverageIgnoreEnd
    $query = new FluentDOM\Query();
    if (isset($source)) {
      $query->load($source, $contentType);
    }
    return $query;
  }

  /**
   * Create an FluentDOM::Query instance with a modified selector callback.
   * This allows to use CSS selectors instead of Xpath expression.
   *
   * @param mixed $source
   * @param string $contentType
   * @return \FluentDOM\Query
   */
  public static function QueryCss($source = NULL, $contentType = 'text/xml') {
    if (!class_exists('PhpCss')) {
      throw new \LogicException('Install "carica/phpcss" to support css selectors.');
    }
    $query = self::Query($source, $contentType);
    $query->onPrepareSelector = function($selector) {
      return \PhpCss::toXpath($selector);
    };
    return $query;
  }
}

/**
 * FluentDOM function, is an Alias for the FluentDOM::Query()
 * factory class function.
 *
 * @param null $source
 * @param string $contentType
 */
function FluentDOM($source = NULL, $contentType = 'text/xml') {
  return FluentDOM::Query($source, $contentType);
}