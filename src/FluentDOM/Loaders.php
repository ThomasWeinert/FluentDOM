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

namespace FluentDOM {

  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Loader\LoaderOptions;
  use FluentDOM\Loader\LoaderResult;

  /**
   * FluentDOM\Loaders is a list of loaders that allow to import data sources into
   * a DOM document.
   *
   * The list is iterated until a valid document is returned by the loader
   *
   */
  class Loaders implements \IteratorAggregate, Loadable {

    private array $_list = [];

    /**
     * Store the a list of loaders if provided.
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
     */
    public function add(Loadable $loader): void {
      $this->_list[spl_object_hash($loader)] = $loader;
    }

    /**
     * Remove a loader to the list
     */
    public function remove(Loadable $loader): void {
      $key = spl_object_hash($loader);
      if (isset($this->_list[$key])) {
        unset($this->_list[$key]);
      }
    }

    /**
     * Allow to iterate all added loaders
     */
    public function getIterator(): \Iterator {
      return new \ArrayIterator(array_values($this->_list));
    }

    /**
     * Validate if the list contains a loader that supports the given content type
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
     */
    public function load(
      mixed $source, string $contentType, iterable $options = []
    ): ?LoaderResult {
      $result = NULL;
      foreach ($this as $loader) {
        /**
         * @var Loadable $loader
         */
        if ($loader->supports($contentType) && ($result = $loader->load($source, $contentType, $options))) {
          break;
        }
      }
      return ($result instanceof LoaderResult) ? $result : NULL;
    }

    /**
     * Load a data source as a fragment, the content type allows the loader to decide if it supports
     * the data source
     */
    public function loadFragment(
      mixed $source, string $contentType, iterable $options = []
    ): ?DocumentFragment {
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
