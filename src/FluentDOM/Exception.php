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

namespace FluentDOM {

  /**
   * FluentDOM\Exception is an interface implemented by FluentDOM specific exceptions.
   *
   * This allow to catch them without handling each specifically.
   */
  interface Exception extends \Throwable {

  }
}
