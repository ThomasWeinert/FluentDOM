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

namespace FluentDOM\Exceptions {

  use FluentDOM\Exception;

  class InvalidArgument extends \InvalidArgumentException implements Exception {

    /**
     * @param string $argumentName
     * @param string|string[] $expectedTypes
     */
    public function __construct(string $argumentName, $expectedTypes = NULL) {
      $message = \sprintf('Invalid $%s argument.', $argumentName);
      if (\is_array($expectedTypes) && \count($expectedTypes) > 0) {
        $message .= ' Expected: '.\implode(', ', $expectedTypes);
      } elseif (NULL !== $expectedTypes) {
        $message .= ' Expected: '.$expectedTypes;
      }
      parent::__construct($message);
    }
  }
}
