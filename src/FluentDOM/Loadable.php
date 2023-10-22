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
  use FluentDOM\Loader\LoaderResult;

  /**
   * FluentDOM\Loadable describes an interface for loader objects that can be used to load
   * a data source into a DOM document.
   *
   * The class can be attached to a FluentDOM\Query objects using the FluentDOM\Loaders class.
   */
  interface Loadable {

    /**
     * Validate if the loader supports the given content type
     */
    public function supports(string $contentType):bool;

    /**
     * Load the data source and return the new DOM document. Return NULL if
     * the data source could not be loaded.
     */
    public function load($source, string $contentType, iterable $options = []): ?LoaderResult;

    /**
     * Load the data source and return the new DOM document. Return NULL if
     * the data source could not be loaded.
     */
    public function loadFragment($source, string $contentType, iterable $options = []): ?DocumentFragment;
  }
}
