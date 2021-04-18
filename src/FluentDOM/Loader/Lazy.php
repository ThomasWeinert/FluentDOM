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

namespace FluentDOM\Loader {

  use FluentDOM\DOM\DocumentFragment;
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
     * @param Loadable|callable $loader
     * @throws \UnexpectedValueException
     */
    public function add(string $contentType, $loader): void {
      $contentType = $this->normalizeContentType($contentType);
      if ($loader instanceof Loadable || \is_callable($loader)) {
        $this->_list[$contentType] = $loader;
        return;
      }
      throw new \UnexpectedValueException(
        \sprintf(
          'Lazy loader for content type "%s" is not a callable or FluentDOM\Loadable',
          $contentType
        )
      );
    }

    /**
     * Add loader classes for different types
     *
     * @param array[]|string[] $classes ['class' => ['type/one', 'type/two'], ...]
     * @param string $namespace
     * @throws \LogicException
     * @throws \UnexpectedValueException
     */
    public function addClasses(array $classes, string $namespace = ''): void {
      foreach ($classes as $loader => $types) {
        $class = \str_replace(['\\\\\\', '\\\\'], '\\', $namespace.'\\'.$loader);
        $callback = function() use ($class) {
          if (!\class_exists($class)) {
            throw new \LogicException(
              \sprintf(
                'Loader class "%s" not found.', $class
              )
            );
          }
          return new $class;
        };
        if (\is_array($types)) {
          foreach ($types as $type) {
            $this->add($type, $callback);
          }
        } else {
          $this->add((string)$types, $callback);
        }
      }
    }

    /**
     * @throws \UnexpectedValueException
     * @param string $contentType
     * @return bool|Loadable
     */
    public function get(string $contentType) {
      $contentType = $this->normalizeContentType($contentType);
      if (isset($this->_list[$contentType])) {
        if (!($this->_list[$contentType] instanceof Loadable)) {
          $this->_list[$contentType] = $this->_list[$contentType]();
        }
        if (!($this->_list[$contentType] instanceof Loadable)) {
          unset($this->_list[$contentType]);
          throw new \UnexpectedValueException(
            \sprintf(
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
    public function supports(string $contentType): bool {
      $contentType = $this->normalizeContentType($contentType);
      return isset($this->_list[$contentType]);
    }

    /**
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Result|NULL
     */
    public function load($source, string $contentType, $options = []): ?Result {
      $contentType = $this->normalizeContentType($contentType);
      if ($loader = $this->get($contentType)) {
        return $loader->load($source, $contentType, $options);
      }
      return NULL;
    }

    /**
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     * @throws \UnexpectedValueException
     */
    public function loadFragment($source, string $contentType, $options = []): ?DocumentFragment {
      $contentType = $this->normalizeContentType($contentType);
      if ($loader = $this->get($contentType)) {
        return $loader->loadFragment($source, $contentType, $options);
      }
      return NULL;
    }

    /**
     * @param string $contentType
     * @return string
     */
    private function normalizeContentType(string $contentType): string {
      return \strtolower(\trim($contentType));
    }
  }
}
