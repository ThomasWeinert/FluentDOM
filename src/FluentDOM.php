<?php

abstract class FluentDOM {

  private static $_load = TRUE;

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