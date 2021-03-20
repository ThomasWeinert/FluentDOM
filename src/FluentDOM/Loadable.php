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
   * FluentDOM\Loadable describes an interface for loader objects that can be used to load
   * a data source into a DOM document.
   *
   * The class can be attached to a FluentDOM\Query objects using the FluentDOM\Loaders class.
   */
  interface Loadable {

    /**
     * Validate if the loader supports the given content type
     *
     * @param string $contentType
     * @return bool
     */
    public function supports(string $contentType):bool;

    /**
     * Load the data source and return the new DOM document. Return NULL if
     * the data source could not be loaded.
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options Optional options for the loader
     * @return Result|NULL
     */
    public function load($source, string $contentType, $options = []): ?Result;

    /**
     * Load the data source and return the new DOM document. Return NULL if
     * the data source could not be loaded.
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options Optional options for the loader
     * @return NULL|DocumentFragment
     */
    public function loadFragment($source, string $contentType, $options = []): ?DocumentFragment;
  }
}
