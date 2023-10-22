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

namespace FluentDOM\Loader\XDM {

  use FluentDOM\Loader\LazyLoaders as LazyLoaders;

  /**
   * Index for json format loaders
   */
  class XDMLoaders extends LazyLoaders {

    private static array $_loaders = [
      JsonAsXDMLoader::class => JsonAsXDMLoader::CONTENT_TYPES
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders);
    }
  }
}
