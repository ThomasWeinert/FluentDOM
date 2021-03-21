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

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class UnattachedNode extends \Exception implements Exception {

    public function __construct() {
      parent::__construct("Node has no owner document and isn't a document.");
    }
  }
}
