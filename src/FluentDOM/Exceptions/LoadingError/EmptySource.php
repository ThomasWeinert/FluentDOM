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

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\Exceptions;

  class EmptySource extends \UnexpectedValueException implements Exceptions\LoadingError {

    public function __construct() {
      parent::__construct(
        'Given source was empty.'
      );
    }

  }
}
