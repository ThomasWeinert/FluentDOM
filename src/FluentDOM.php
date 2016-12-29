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
   * @var FluentDOM\Serializer\Factory\Group
   */
  private static $_serializerFactories = NULL;

  /**
   * Load a data source into a FluentDOM\Document
   *
   * @param mixed $source
   * @param string $contentType
   * @param array $options
   * @return \FluentDOM\Document
   */
  public static function load($source, $contentType = 'text/xml', array $options = []) {
    if (!isset(self::$_loader)) {
      self::$_loader = self::getDefaultLoaders();
    }
    $result = self::$_loader->load($source, $contentType, $options);
    return $result instanceof \DOMDocument ? $result : $result->getDocument();
  }

  /**
   * Return a FluentDOM Creator instance, allow to create a DOM using nested function calls
   *
   * @param string $version
   * @param string $encoding
   * @return \FluentDOM\Nodes\Creator
   */
  public static function create($version = '1.0', $encoding = 'UTF-8') {
    return new \FluentDOM\Nodes\Creator($version, $encoding);
  }

  /**
   * @param \DOMNode|\FluentDOM\Query $node
   * @param string $contentType
   * @return string
   */
  public static function save($node, $contentType = 'text/xml') {
    if ($node instanceof \FluentDOM\Query) {
      $node = $node->document;
    }
    if ($serializer = self::getSerializerFactories()->createSerializer($contentType, $node)) {
      return (string)$serializer;
    }
    throw new \FluentDOM\Exceptions\NoSerializer($contentType);
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
   * @param \FluentDOM\Loadable|callable $loader
   * @param [string] ...$contentTypes
   * @return \FluentDOM\Loaders
   */
  public static function registerLoader($loader, ...$contentTypes) {
    $loaders = self::getDefaultLoaders();
    if (count($contentTypes) > 0) {
      $lazyLoader = new \FluentDOM\Loader\Lazy();
      foreach ($contentTypes as $contentType) {
        $lazyLoader->add($contentType, $loader);
      }
      $loaders->add($lazyLoader);
    } else if (is_callable($loader)) {
      $loaders->add($loader());
    } else {
      $loaders->add($loader);
    }
    self::$_loader = NULL;
    return $loaders;
  }

  /**
   * Standard loader + any registered loader.
   *
   * @return \FluentDOM\Loaders
   * @codeCoverageIgnore
   */
  public static function getDefaultLoaders() {
    if (!(self::$_defaultLoaders instanceof FluentDOM\Loaders)) {
      self::$_defaultLoaders = new FluentDOM\Loaders(new FluentDOM\Loader\Standard());
    }
    return self::$_defaultLoaders;
  }

  /**
   * Register a serializer factory for a specified content type(s). This can be
   * a callable returning the create serializer.
   *
   * @param \FluentDOM\Serializer\Factory|callable $factory
   * @param [string] ...$contentTypes
   */
  public static function registerSerializerFactory($factory, ...$contentTypes) {
    foreach ($contentTypes as $contentType) {
      self::getSerializerFactories()[$contentType] = $factory;
    }
  }

  /**
   * Return registered serializer factories
   *
   * @return \FluentDOM\Serializer\Factory\Group
   */
  public static function getSerializerFactories() {
    if (!(self::$_serializerFactories instanceof FluentDOM\Serializer\Factory)) {
      $xml = function($contentType, \DOMNode $node) {
        return new FluentDOM\Serializer\Xml($node);
      };
      $html = function($contentType, \DOMNode $node) {
        return new FluentDOM\Serializer\Html($node);
      };
      $json = function($contentType, \DOMNode $node) {
        return new FluentDOM\Serializer\Json($node);
      };
      self::$_serializerFactories = new FluentDOM\Serializer\Factory\Group(
        [
          'text/html' => $html,
          'html' => $html,
          'text/xml' => $xml,
          'xml' => $xml,
          'text/json' => $json,
          'json' => $json
        ]
      );
    }
    return self::$_serializerFactories;
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
    if ($reset) {
      self::$_xpathTransformers = [];
    }
    array_unshift(self::$_xpathTransformers, $transformer);
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
