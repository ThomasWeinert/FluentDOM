<?php
/**
 * A list of lazy initialized loaders.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {
  use FluentDOM\Loadable;

  /**
   * A list of lazy initialized loaders.
   */
  class Lazy implements Loadable {

    private $_list = [];

    public function __construct(array $loaders = []) {
      foreach ($loaders as $contentType => $loader) {
        $this->add($contentType, $loader);
      }
    }

    /**
     * Add a loader to the list
     *
     * @param string $contentType
     * @param Loadable|\callable $loader
     * @throws \UnexpectedValueException
     */
    public function add($contentType, $loader) {
      $contentType = $this->normalizeContentType($contentType);
      if ($loader instanceof Loadable || is_callable($loader)) {
        $this->_list[$contentType] = $loader;
        return;
      }
      throw new \UnexpectedValueException(
        sprintf(
          'Lazy loader for content type "%s" is not a callable or FluentDOM\Loadable',
          $contentType
        )
      );
    }

    /**
     * Add loader classes for different types
     *
     * @param array[] $classes ['class' => ['type/one', 'type/two'], ...]
     * @param string $namespace
     */
    public function addClasses($classes, $namespace = '') {
      $namespace = substr($namespace, -1) === '\\' ? substr($namespace, 0, -1) : $namespace;
      foreach ($classes as $loader => $types) {
        $loader = substr($loader, 0, 1) === '\\' ? $loader : '\\'.$loader;
        $class = $namespace.$loader;
        $callback = function() use ($class) {
          if (!class_exists($class)) {
            throw new \LogicException(
              sprintf(
                'Loader class "%s" not found.', $class
              )
            );
          }
          return new $class;
        };
        if (is_array($types)) {
          foreach ($types as $type) {
            $this->add($type, $callback);
          }
        } else {
          $this->add($types, $callback);
        }
      }
    }

    /**
     * @throws \UnexpectedValueException
     * @param string $contentType
     * @return bool|Loadable
     */
    public function get($contentType) {
      $contentType = $this->normalizeContentType($contentType);
      if (isset($this->_list[$contentType])) {
        if (!($this->_list[$contentType] instanceof Loadable)) {
          $this->_list[$contentType] = call_user_func($this->_list[$contentType]);
        }
        if (!($this->_list[$contentType] instanceof Loadable)) {
          unset($this->_list[$contentType]);
          throw new \UnexpectedValueException(
            sprintf(
              'Lazy loader for content type "%s" did not return a FluentDOM\Loadable',
              $contentType
            )
          );
        }
        return $this->_list[$contentType];
      }
      return FALSE;
    }

    /**
     * @param string $contentType
     * @return bool
     */
    public function supports($contentType) {
      $contentType = $this->normalizeContentType($contentType);
      return isset($this->_list[$contentType]);
    }

    /**
     * @param string $source
     * @param mixed $contentType
     * @param array $options
     * @return \DOMDocument|NULL|void
     */
    public function load($source, $contentType, array $options = []) {
      $contentType = $this->normalizeContentType($contentType);
      if ($loader = $this->get($contentType)) {
        return $loader->load($source, $contentType, $options);
      }
      return NULL;
    }

    private function normalizeContentType($contentType) {
      return trim(strtolower($contentType));
    }
  }
}