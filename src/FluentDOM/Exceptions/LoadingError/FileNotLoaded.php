<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2020 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\Exceptions\LoadingError {

  use FluentDOM\Exceptions;

  class FileNotLoaded extends \UnexpectedValueException implements Exceptions\LoadingError {

    public function __construct(string $fileName) {
      parent::__construct(
        sprintf(
          'Could not load file: %s', $fileName
        )
      );
    }

  }
}
