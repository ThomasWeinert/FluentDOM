<?php
/**
 * FluentDOM\Loadable describes an interface for loader objects that can be used to load
 * a data source into a DOM document.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM {

  use FluentDOM\Loader\Options;

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
     * @return \FluentDOM\DOM\Document|\FluentDOM\Loader\Result|NULL
     */
    public function load($source, string $contentType, $options = []);

    /**
     * Load the data source and return the new DOM document. Return NULL if
     * the data source could not be loaded.
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options Optional options for the loader
     * @return NULL|\FluentDOM\DOM\DocumentFragment
     */
    public function loadFragment($source, string $contentType, $options = []);
  }
}