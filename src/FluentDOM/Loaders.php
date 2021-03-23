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

namespace FluentDOM {

  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Result;

  /**
   * FluentDOM\Loaders is a list of loaders that allow to import data sources into
   * a DOM document.
   *
   * The list is iterated until a valid document is returned by the loader
   *
   */
  class Loaders implements \IteratorAggregate, Loadable {

    private $_list = [];

    /**
     * Store the a list of loaders if provided.
     *
     * @param iterable|NULL $list
     */
    public function __construct(iterable $list = NULL) {
      if (is_iterable($list)) {
        /** @var array|\Traversable $list */
        foreach ($list as $loader) {
          $this->add($loader);
        }
      }
    }

    /**
     * Add a loader to the list
     *
     * @param Loadable $loader
     */
    public function add(Loadable $loader) {
      $this->_list[spl_object_hash($loader)] = $loader;
    }

    /**
     * Remove a loader to the list
     *
     * @param Loadable $loader
     */
    public function remove(Loadable $loader) {
      $key = spl_object_hash($loader);
      if (isset($this->_list[$key])) {
        unset($this->_list[$key]);
      }
    }

    /**
     * Allow to iterate all added loaders
     *
     * @return \Iterator
     */
    public function getIterator(): \Iterator {
      return new \ArrayIterator(array_values($this->_list));
    }

    /**
     * Validate if the list contains a loader that supports the given content type
     *
     * @param string $contentType
     * @return bool
     */
    public function supports(string $contentType): bool {
      foreach ($this as $loader) {
        /**
         * @var Loadable $loader
         */
        if ($loader->supports($contentType)) {
          return TRUE;
        }
      }
      return FALSE;
    }

    /**
     * Load a data source, the content type allows the loader to decide if it supports
     * the data source
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Result|NULL
     */
    public function load($source, string $contentType, $options = []): ?Result {
      $result = NULL;
      foreach ($this as $loader) {
        /**
         * @var Loadable $loader
         */
        if ($loader->supports($contentType) && ($result = $loader->load($source, $contentType, $options))) {
          break;
        }
      }
      return ($result instanceof Result) ? $result : NULL;
    }

    /**
     * Load a data source as a fragment, the content type allows the loader to decide if it supports
     * the data source
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment($source, string $contentType, $options = []): ?DocumentFragment {
      $fragment = NULL;
      foreach ($this as $loader) {
        /**
         * @var Loadable $loader
         */
        if (
          $loader->supports($contentType) &&
          ($fragment = $loader->loadFragment($source, $contentType, $options))
        ) {
          break;
        }
      }
      return ($fragment instanceOf DocumentFragment) ? $fragment : NULL;
    }
  }
}
