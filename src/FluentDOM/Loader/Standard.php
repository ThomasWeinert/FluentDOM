<?php
/**
 * Encapsulates the standard loaders (html, xml, json)
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Loader {

  use FluentDOM\Loader;
  use FluentDOM\Loaders;

  /**
 * Encapsulates the standard loaders (html, xml, json)
   */
  class Standard extends Loaders {

    public function __construct() {
      parent::__construct(
        [
          new Loader\Xml(),
          new Loader\Html(),
          new Loader\Text(),
          new Loader\Json(),
          new Loader\PHP()
        ]
      );
    }
  }
}