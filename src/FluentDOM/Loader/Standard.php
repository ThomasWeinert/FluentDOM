<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader {

  use FluentDOM\Loader;
  use FluentDOM\Loaders;

  /**
 * Encapsulates the standard loaders (html, xml, json, ...)
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
