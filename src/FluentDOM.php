<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

use FluentDOM\Creator;
use FluentDOM\DOM\Document;
use FluentDOM\Exceptions\InvalidArgument;
use FluentDOM\Exceptions\InvalidSource\Variable as InvalidVariableSource;
use FluentDOM\Exceptions\NoSerializer as NoSerializerException;
use FluentDOM\Loadable;
use FluentDOM\Loader\Lazy as LazyLoader;
use FluentDOM\Serializer\Factory as SerializerFactory;
use FluentDOM\Xpath\Transformer as XpathTransformer;

abstract class FluentDOM {

  private static ?Loadable $_loader = NULL;

  private static array $_xpathTransformers = [];

  private static array|Loadable $_defaultLoaders = [];

  private static ?SerializerFactory\Group $_serializerFactories = NULL;

  /**
   * Load a data source into a FluentDOM\DOM\Document
   */
  public static function load(
    mixed $source,
    string $contentType = 'text/xml',
    array $options = []
  ): Document {
    if (NULL === self::$_loader) {
      self::$_loader = self::getDefaultLoaders();
    }
    $result = self::$_loader->load($source, $contentType, $options);
    return $result->getDocument();
  }

  /**
   * Return a FluentDOM Creator instance, allow to create a DOM using nested function calls
   */
  public static function create(string $version = '1.0', string $encoding = 'UTF-8'): Creator {
    return new FluentDOM\Creator($version, $encoding);
  }

  public static function save(DOMNode|FluentDOM\Query $node, string $contentType = 'text/xml'): string {
    if ($node instanceof FluentDOM\Query) {
      $node = $node->document;
    }
    if ($serializer = self::getSerializerFactories()->createSerializer($node, $contentType)) {
      return (string)$serializer;
    }
    throw new NoSerializerException($contentType);
  }

  /**
   * Create an FluentDOM::Query instance and load the source into it.
   *
   * @throws LogicException
   * @throws OutOfBoundsException
   * @throws InvalidArgumentException
   * @throws InvalidVariableSource
   */
  public static function Query(
    mixed $source = NULL,
    string $contentType = 'text/xml',
    array $options = []
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
   * @throws LogicException
   * @throws InvalidArgumentException
   * @throws OutOfBoundsException
   * @throws InvalidVariableSource
   */
  public static function QueryCss(
    mixed $source = NULL,
    string $contentType = 'text/xml',
    array $options = []
  ): FluentDOM\Query {
    $transformer = self::getXPathTransformer();
    $query = self::Query($source, $contentType, $options);
    $isHtml = ($query->contentType === 'text/html');
    $query->onPrepareSelector = static function($selector, $contextMode) use ($transformer, $isHtml) {
      return $transformer->toXpath($selector, $contextMode, $isHtml);
    };
    return $query;
  }

  /**
   * Set a loader used in FluentDOM::load(), FALSE will reset the loader.
   * If no loader is provided an FluentDOM\Loader\Standard() will be created.
   *
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
   */
  public static function registerLoader(
    Loadable|callable $loader,
    string ...$contentTypes
  ): FluentDOM\Loaders {
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
   */
  public static function registerSerializerFactory(
    SerializerFactory|callable $factory,
    string ...$contentTypes
  ): void {
    foreach ($contentTypes as $contentType) {
      self::getSerializerFactories()[$contentType] = $factory;
    }
  }

  /**
   * Return registered serializer factories
   */
  public static function getSerializerFactories(): SerializerFactory\Group {
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

  public static function registerXpathTransformer(
    string|callable|XpathTransformer $transformer,
    bool $reset = FALSE
  ): void {
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
 * @codeCoverageIgnore
 */
function FluentDOM(
  mixed $source = NULL,
  string $contentType = 'text/xml',
  array $options = []
): FluentDOM\Query {
  return FluentDOM::Query($source, $contentType, $options);
}
