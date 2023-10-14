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
   * Index for text format loaders
   */
  class Text extends Lazy {

    private static array $_loaders = [
      Text\CSV::class => Text\CSV::CONTENT_TYPES
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders);
    }
  }
}
