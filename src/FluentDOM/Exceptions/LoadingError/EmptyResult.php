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

  class EmptyResult extends \UnexpectedValueException implements Exceptions\LoadingError {

    public function __construct() {
      parent::__construct(
        'Parsing result did not contain an usable node.'
      );
    }

  }
}
