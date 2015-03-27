<?php

abstract class FluentDOM {

  /**
   * @var bool
   */
  public static $isHHVM = FALSE;

  /**
   * @var FluentDOM\Loadable
   */
  private static $_loader = NULL;

  /**
   * @var array
   */
  private static $_xpathTransformers = [];

  /**
   * @var FluentDOM\Loadable
   */
  private static $_defaultLoaders = [];

  /**
   * Load a data source into a FluentDOM\Document
   *
   * @param mixed $source
   * @param string $contentType
   * @param array $options
   * @return \FluentDOM\Document
   */
  public static function load($source, $contentType = 'text/xml', array $options = []) {
    self::_require();
    if (!isset(self::$_loader)) {
      self::$_loader = self::getDefaultLoaders();
    }
    return self::$_loader->load($source, $contentType, $options);
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
   * Create an FluentDOM::Query instance and load the source into it.
   *
   * @param mixed $source
   * @param string $contentType
   * @param array $options
   * @return \FluentDOM\Query
   */
  public static function Query($source = NULL, $contentType = 'text/xml', array $options = []) {
    self::_require();
    $query = new FluentDOM\Query();
    if (isset($source)) {
      $query->load($source, $contentType, $options);
    }
    return $query;
  }

  /**
   * Create an FluentDOM::Query instance with a modified selector callback.
   * This allows to use CSS selectors instead of Xpath expression.
   *
   * @param mixed $source
   * @param string $contentType
   * @param array $options
   * @throws \LogicException
   * @return \FluentDOM\Query
   * @codeCoverageIgnore
   */
  public static function QueryCss($source = NULL, $contentType = 'text/xml', array $options = []) {
    $builder = self::getXPathTransformer();
    $query = self::Query($source, $contentType, $options);
    $isHtml = ($query->contentType === 'text/html');
    $query->onPrepareSelector = function($selector, $contextMode) use ($builder, $isHtml) {
      return $builder->toXpath($selector, $contextMode, $isHtml);
    };
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
   * Register an additional default loader
   *
   * @param \FluentDOM\Loadable $loader
   * @return \FluentDOM\Loaders
   */
  public static function registerLoader(FluentDOM\Loadable $loader) {
    $loaders = self::getDefaultLoaders();
    $loaders->add($loader);
    self::$_loader = NULL;
    return $loaders;
  }

  /**
   * Standard loader + any registered loader.
   *
   * @return array|\FluentDOM\Loaders
   * @codeCoverageIgnore
   */
  public static function getDefaultLoaders() {
    if (!(self::$_defaultLoaders instanceof FluentDOM\Loaders)) {
      self::$_defaultLoaders = new FluentDOM\Loaders(new FluentDOM\Loader\Standard());
    }
    return self::$_defaultLoaders;
  }

  /**
   * Get a xpath expression builder to convert css selectors to xpath
   *
   * @param string $errorMessage
   * @return \FluentDOM\Xpath\Transformer
   */
  public static function getXPathTransformer($errorMessage = 'No CSS selector support installed') {
    foreach (FluentDOM::$_xpathTransformers as $index => $transformer) {
      if (is_string($transformer) && class_exists($transformer)) {
        FluentDOM::$_xpathTransformers[$index] = new $transformer();
      } elseif (is_callable($transformer)) {
        FluentDOM::$_xpathTransformers[$index] = $transformer();
      }
      if (FluentDOM::$_xpathTransformers[$index] instanceof \FluentDOM\Xpath\Transformer) {
        return FluentDOM::$_xpathTransformers[$index];
      } else {
        unset(FluentDOM::$_xpathTransformers[$index]);
      }
    }
    throw new \LogicException($errorMessage);
  }

  /**
   * @param string|callable|FluentDOM\Xpath\Transformer $transformer
   */
  public static function registerXpathTransformer($transformer, $reset = FALSE) {
    self::_require();
    if ($reset) {
      self::$_xpathTransformers = [];
    }
    array_unshift(self::$_xpathTransformers, $transformer);
  }

  /**
   * Try using autoloader. If is not available, use the _require.php
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
FluentDOM::$isHHVM = defined('HHVM_VERSION');


/**
 * FluentDOM function, is an Alias for the \FluentDOM\FluentDOM::Query()
 * factory class function.
 *
 * @param mixed $source
 * @param string $contentType
 * @param array $options
 * @return \FluentDOM\Query
 * @codeCoverageIgnore
 */
function FluentDOM($source = NULL, $contentType = 'text/xml', array $options = []) {
  return FluentDOM::Query($source, $contentType, $options);
}
