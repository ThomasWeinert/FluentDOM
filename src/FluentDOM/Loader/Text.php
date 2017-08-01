<?php
/**
 * Index for text format loaders
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Loader {

  /**
   * Index for text format loaders
   */
  class Text extends Lazy {

    private static $_loaders = [
      '\\Text\\CSV' => ['text/csv']
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders, __NAMESPACE__);
    }
  }
}