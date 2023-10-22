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

namespace FluentDOM\Loader {

  /**
   * A lazy load group for php class loaders
   *
   * This defines loaders for PHPLoader classes like SimpleXMLLoader
   */
  class PHPLoaders extends LazyLoaders {

    private static array $_loaders = [
      PHP\PDOLoader::class => PHP\PDOLoader::CONTENT_TYPES,
      PHP\SimpleXmlLoader::class => PHP\SimpleXmlLoader::CONTENT_TYPES
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders);
    }
  }
}
