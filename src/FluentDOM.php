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

use FluentDOM\Creator;
use FluentDOM\DOM\Document;
use FluentDOM\Exceptions\InvalidArgument;
use FluentDOM\Exceptions\InvalidSource\Variable as InvalidVariableSource;
use FluentDOM\Exceptions\NoSerializer;
use FluentDOM\Loadable;
use FluentDOM\Loader\Lazy as LazyLoader;
use FluentDOM\Serializer\Factory as SerializerFactory;
use FluentDOM\Xpath\Transformer as XpathTransformer;

abstract class FluentDOM {

  /**
   * @var FluentDOM\Loadable
   */
  private static $_loader;

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
  private static $_serializerFactories;

  /**
   * Load a data source into a FluentDOM\DOM\Document
   *
   * @param mixed $source
   * @param string $contentType
   * @param array $options
   * @return Document
   */
  public static function load($source, string $contentType = 'text/xml', array $options = []): Document {
    if (NULL === self::$_loader) {
      self::$_loader = self::getDefaultLoaders();
    }
    $result = self::$_loader->load($source, $contentType, $options);
    return $result->getDocument();
  }

  /**
   * Return a FluentDOM Creator instance, allow to create a DOM using nested function calls
   *
   * @param string $version
   * @param string $encoding
   * @return FluentDOM\Creator
   */
  public static function create(string $version = '1.0', string $encoding = 'UTF-8'): Creator {
    return new FluentDOM\Creator($version, $encoding);
  }

  /**
   * @param DOMNode|FluentDOM\Query $node
   * @param string $contentType
   * @return string
   * @throws NoSerializer
   */
  public static function save($node, string $contentType = 'text/xml'): string {
    if ($node instanceof FluentDOM\Query) {
      $node = $node->document;
    }
    if ($serializer = self::getSerializerFactories()->createSerializer($node, $contentType)) {
      return (string)$serializer;
    }
    throw new FluentDOM\Exceptions\NoSerializer($contentType);
  }

  /**
   * Create an FluentDOM::Query instance and load the source into it.
   *
   * @param mixed $source
   * @param string $contentType
   * @param array $options
   * @return FluentDOM\Query
   * @throws LogicException
   * @throws OutOfBoundsException
   * @throws InvalidArgumentException
   * @throws InvalidVariableSource
   */
  public static function Query(
    $source = NULL, string $contentType = 'text/xml', array $options = []
  ): FluentDOM\Query {
    $query = new FluentDOM\Query();
    if (NULL !== $source) {
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
   * @return FluentDOM\Query
   * @throws LogicException
   * @throws InvalidArgumentException
   * @throws OutOfBoundsException
   * @throws InvalidVariableSource
   * @codeCoverageIgnore
   */
  public static function QueryCss(
    $source = NULL, string $contentType = 'text/xml', array $options = []
  ): FluentDOM\Query {
    $builder = self::getXPathTransformer();
    $query = self::Query($source, $contentType, $options);
    $isHtml = ($query->contentType === 'text/html');
    $query->onPrepareSelector = static function($selector, $contextMode) use ($builder, $isHtml) {
      return $builder->toXpath($selector, $contextMode, $isHtml);
    };
    return $query;
  }

  /**
   * Set a loader used in FluentDOM::load(), FALSE will reset the loader.
   * If no loader is provided an FluentDOM\Loader\Standard() will be created.
   *
   * @param FluentDOM\Loadable|NULL $loader
   * @throws InvalidArgument
   */
  public static function setLoader(Loadable $loader = NULL): void {
    if (!$loader) {
      self::$_loader = NULL;
      return;
    }
    self::$_loader = $loader;
  }

  /**
   * Register an additional default loader
   *
   * @param FluentDOM\Loadable|callable $loader
   * @param string ...$contentTypes
   * @return FluentDOM\Loaders
   */
  public static function registerLoader($loader, string ...$contentTypes): FluentDOM\Loaders {
    $loaders = self::getDefaultLoaders();
    if (count($contentTypes) > 0) {
      $lazyLoader = new LazyLoader();
      foreach ($contentTypes as $contentType) {
        $lazyLoader->add($contentType, $loader);
      }
      $loaders->add($lazyLoader);
    } elseif (is_callable($loader)) {
      self::registerLoader($loader());
    } else {
      $loaders->add($loader);
    }
    self::$_loader = NULL;
    return $loaders;
  }

  /**
   * Standard loader + any registered loader.
   *
   * @return FluentDOM\Loaders
   * @codeCoverageIgnore
   */
  public static function getDefaultLoaders(): FluentDOM\Loaders {
    if (!(self::$_defaultLoaders instanceof FluentDOM\Loaders)) {
      self::$_defaultLoaders = new FluentDOM\Loaders(new FluentDOM\Loader\Standard());
    }
    return self::$_defaultLoaders;
  }

  /**
   * Register a serializer factory for a specified content type(s). This can be
   * a callable returning the create serializer.
   *
   * @param SerializerFactory|callable $factory
   * @param string ...$contentTypes
   */
  public static function registerSerializerFactory(
    $factory, string ...$contentTypes
  ): void {
    foreach ($contentTypes as $contentType) {
      self::getSerializerFactories()[$contentType] = $factory;
    }
  }

  /**
   * Return registered serializer factories
   *
   * @return FluentDOM\Serializer\Factory\Group
   */
  public static function getSerializerFactories(): FluentDOM\Serializer\Factory\Group {
    if (NULL === self::$_serializerFactories) {
      $xml = static function(DOMNode $node) {
        return new FluentDOM\Serializer\Xml($node);
      };
      $html = static function(DOMNode $node) {
        return new FluentDOM\Serializer\Html($node);
      };
      $json = static function(DOMNode $node) {
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
   * @return XpathTransformer
   * @throws LogicException
   */
  public static function getXPathTransformer(
    string $errorMessage = 'No CSS selector support installed'
  ): XpathTransformer {
    foreach (self::$_xpathTransformers as $index => $transformer) {
      if (is_string($transformer) && class_exists($transformer)) {
        self::$_xpathTransformers[$index] = new $transformer();
      } elseif (is_callable($transformer)) {
        self::$_xpathTransformers[$index] = $transformer();
      }
      if (self::$_xpathTransformers[$index] instanceof XpathTransformer) {
        return self::$_xpathTransformers[$index];
      }
      unset(self::$_xpathTransformers[$index]);
    }
    throw new LogicException($errorMessage);
  }

  /**
   * @param string|callable|FluentDOM\Xpath\Transformer $transformer
   * @param bool $reset
   */
  public static function registerXpathTransformer($transformer, bool $reset = FALSE): void {
    if ($reset) {
      self::$_xpathTransformers = [];
    }
    array_unshift(self::$_xpathTransformers, $transformer);
  }
}


/**
 * FluentDOM function, is an Alias for the \FluentDOM\FluentDOM::Query()
 * factory class function.
 *
 * @param mixed $source
 * @param string $contentType
 * @param array $options
 * @return FluentDOM\Query
 * @codeCoverageIgnore
 */
function FluentDOM($source = NULL, string $contentType = 'text/xml', array $options = []): FluentDOM\Query {
  return FluentDOM::Query($source, $contentType, $options);
}
