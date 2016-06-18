<?php
/**
 * FluentDOM\Loadable\Fragment describes an interface for loader objects that can be used to load
 * a data source into a DOM document fragment. This is used to load document parts that can be appended to existing
 * documents.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loadable {

  interface Fragment extends \FluentDOM\Loadable {

    /**
     * Load the data source and return the new DOM document. Return NULL if
     * the data source could not be loaded.
     *
     * @param mixed $source
     * @param string $contentType
     * @param array $options Optional options for the loader
     * @return NULL|\FluentDOM\DocumentFragment
     */
    public function loadFragment($source, $contentType, array $options = []);
  }
}
