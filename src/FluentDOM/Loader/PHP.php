<?php
/**
 * A lazy load group for php class loaders
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Loader {

  /**
   * A lazy load group for php class loaders
   *
   * This defines loaders for PHP classes like SimpleXML
   */
  class PHP extends Lazy {

    private static $_loaders = [
      '\PHP\PDO' => ['php/pdo', 'pdo'],
      '\PHP\SimpleXml' => ['php/simplexml', 'simplexml']
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders, __NAMESPACE__);
    }
  }
}