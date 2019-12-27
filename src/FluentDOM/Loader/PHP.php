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

  /**
   * A lazy load group for php class loaders
   *
   * This defines loaders for PHP classes like SimpleXML
   */
  class PHP extends Lazy {

    private static $_loaders = [
      PHP\PDO::class => PHP\PDO::CONTENT_TYPES,
      PHP\SimpleXml::class => PHP\SimpleXml::CONTENT_TYPES
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders);
    }
  }
}
