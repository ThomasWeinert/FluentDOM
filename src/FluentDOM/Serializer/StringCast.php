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

namespace FluentDOM\Serializer {

  use FluentDOM\Utility\StringCastable;

  class StringCast implements StringCastable {
    /**
     * @var object
     */
    private $_serializer;

    /**
     * @param object $serializer
     */
    public function __construct(object $serializer) {
      $this->_serializer = $serializer;
    }

    /**
     * @return string
     */
    public function __toString(): string {
      return (string)$this->_serializer;
    }
  }
}
