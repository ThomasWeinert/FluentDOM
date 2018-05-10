<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class NoSerializer extends \UnexpectedValueException implements Exception {

    public function __construct(string $contentType) {
      parent::__construct(
        \sprintf(
          'No serializer for content type %s available.',
          $contentType
        )
      );
    }
  }
}
