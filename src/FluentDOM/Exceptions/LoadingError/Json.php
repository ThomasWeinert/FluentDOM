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

  class Json extends \UnexpectedValueException implements Exceptions\LoadingError {

    /**
     * JSON errors
     * @var array $_jsonErrors
     */
    private static $_jsonErrors = [
      -1 => 'Unknown error has occurred',
      0 => 'No error has occurred',
      1 => 'The maximum stack depth has been exceeded',
      3 => 'Control character error, possibly incorrectly encoded',
      4 => 'Syntax error',
    ];

    /**
     * @param int $code
     */
    public function __construct(int $code) {
      parent::__construct(
        self::$_jsonErrors[$code] ?? self::$_jsonErrors[-1],
        $code
      );
    }

  }
}
