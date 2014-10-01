<?php

use Symfony\Component\CssSelector\CssSelector;

abstract class FluentDOM {

  /**
   * @var FluentDOM\Loadable
   */
  private static $_loader = null;

  /**
   * Create an FluentDOM::Query instance and load the source into it.
   *
   * @param mixed $source
   * @param string $contentType
   * @return \FluentDOM\Query
   */
  public static function Query($source = NULL, $contentType = 'text/xml') {
    self::_require();
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
   * @throws \LogicException
   * @return \FluentDOM\Query
   * @codeCoverageIgnore
   */
  public static function QueryCss($source = NULL, $contentType = 'text/xml') {
    $hasPhpCss = class_exists('PhpCss');
    $hasCssSelector = class_exists('Symfony\Component\CssSelector\CssSelector');
    if (!($hasPhpCss || $hasCssSelector)) {
      throw new \LogicException(
        'Install "carica/phpcss" or "symfony/css-selector" to support css selectors.'
      );
    }
    $query = self::Query($source, $contentType);
    $isHtml = ($query->contentType == 'text/html');
    if ($hasPhpCss) {
      $query->onPrepareSelector = function($selector, $mode) {
        return self::prepareWithPhpCss($selector, $mode);
      };
    } else {
      $query->onPrepareSelector = function($selector, $mode) use ($isHtml) {
        return self::prepareWithCssSelector($selector, $mode, $isHtml);
      };
    }
    return $query;
  }

  /**
   * Set a loader used in FluentDOM::load(), NULL will reset the loader.
   * If no loader is provided an FluentDOM\Loader\Standard() will be created.
   *
   * @param FluentDOM\Loadable|NULL $loader
   */
  public static function setLoader($loader) {
    self::_require();
    if ($loader instanceof \FluentDOM\Loadable) {
      self::$_loader = $loader;
    } elseif (NULL === $loader) {
      self::$_loader = NULL;
    } else {
      throw new \FluentDOM\Exceptions\InvalidArgument(
        'loader', ['FluentDOM\Loadable']
      );
    }
  }

  /**
   * Load a data source into a FluentDOM\Document
   *
   * @param mixed $source
   * @param string $contentType
   * @return \FluentDOM\Document
   */
  public static function load($source, $contentType = 'text/xml') {
    self::_require();
    if (!isset(self::$_loader)) {
      self::$_loader = new FluentDOM\Loader\Standard();
    }
    return self::$_loader->load($source, $contentType);
  }

  /**
   * Return a FluentDOM Creator instance, allow to create a DOM using nested function calls
   *
   * @param string $version
   * @param string $encoding
   * @return \FluentDOM\Nodes\Creator
   */
  public static function create($version = '1.0', $encoding = 'UTF-8') {
    self::_require();
    return new \FluentDOM\Nodes\Creator($version, $encoding);
  }

  /**
   * Convert css selector to xpath with Carica/PhpCss
   *
   * @param string $selector
   * @param int $mode
   * @return string
   * @codeCoverageIgnore
   */
  private static function prepareWithPhpCss($selector, $mode) {
    $options = 0;
    switch ($mode) {
    case FluentDOM\Nodes::CONTEXT_SELF :
      $options = \PhpCss\Ast\Visitor\Xpath::OPTION_USE_CONTEXT_SELF;
      break;
    case FluentDOM\Nodes::CONTEXT_DOCUMENT :
      $options = \PhpCss\Ast\Visitor\Xpath::OPTION_USE_CONTEXT_DOCUMENT;
      break;
    }
    return \PhpCss::toXpath($selector, $options);
  }

  /**
   * Convert css selector to xpath with Symfony/CssSelector
   *
   * @param string $selector
   * @param int $mode
   * @param bool $isHtml
   * @return string
   * @codeCoverageIgnore
   */
  private static function prepareWithCssSelector($selector, $mode, $isHtml) {
    if ($isHtml) {
      CssSelector::enableHtmlExtension();
    } else {
      CssSelector::disableHtmlExtension();
    }
    $result = CssSelector::toXpath($selector);
    switch ($mode) {
    case FluentDOM\Nodes::CONTEXT_CHILDREN :
      return './'.$result;
    case FluentDOM\Nodes::CONTEXT_DOCUMENT :
      return '/'.$result;
    }
    return $result;
  }

  /**
   * Try autoloading. If is not available, use the _require.php
   *
   * Try only once, if the source it not here it will
   * not exists in the second call.
   *
   * @codeCoverageIgnore
   */
  private static function _require() {
    static $load = TRUE;
    if ($load && !interface_exists('FluentDOM\Appendable')) {
      include(__DIR__.'/_require.php');
    }
    $load = FALSE;
  }
}

/**
 * FluentDOM function, is an Alias for the \FluentDOM\FluentDOM::Query()
 * factory class function.
 *
 * @param mixed $source
 * @param string $contentType
 * @return \FluentDOM\Query
 */
function FluentDOM($source = NULL, $contentType = 'text/xml') {
  return FluentDOM::Query($source, $contentType);
}
