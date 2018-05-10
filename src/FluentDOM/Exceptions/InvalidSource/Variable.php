<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Exceptions\InvalidSource {

  use FluentDOM\Exceptions;

  class Variable extends \InvalidArgumentException implements Exceptions\InvalidSource {

    /**
     * @param mixed $source
     * @param string $contentType
     */
    public function __construct($source, string $contentType) {
      parent::__construct(
        \sprintf(
          'Can not load %s as "%s".',
          (\is_object($source) ? \get_class($source) : \gettype($source)),
          $contentType
        )
      );
    }
  }
}
