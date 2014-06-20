<?php

use Symfony\Component\CssSelector;

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
      $query->onPrepareSelector = function($selector) {
        return \PhpCss::toXpath($selector);
      };
    } else {
      $query->onPrepareSelector = function($selector) use ($isHtml) {
        $translator = new CssSelector\Xpath\Translator();
        if ($isHtml) {
          $translator->registerExtension(
            new CssSelector\Xpath\Extension\HtmlExtension($translator)
          );
        }
        $translator
          ->registerParserShortcut(new CssSelector\Parser\Shortcut\EmptyStringParser())
          ->registerParserShortcut(new CssSelector\Parser\Shortcut\ElementParser())
          ->registerParserShortcut(new CssSelector\Parser\Shortcut\ClassParser())
          ->registerParserShortcut(new CssSelector\Parser\Shortcut\HashParser())
        ;
        $result = $translator->cssToXPath($selector);
        return $result;
      };
    }
    return $query;
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