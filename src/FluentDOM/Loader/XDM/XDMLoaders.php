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

namespace FluentDOM\Loader\XDM {

  use FluentDOM\Loader\Lazy as LazyLoaders;

  /**
   * Index for json format loaders
   */
  class XDMLoaders extends LazyLoaders {

    private static $_loaders = [
      JsonAsXDM::class => JsonAsXDM::CONTENT_TYPES
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders);
    }
  }
}
