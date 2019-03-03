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

namespace FluentDOM\Serializer {

  use FluentDOM\Utility\StringCastable;

  interface Factory {

    /**
     * Return a serializer for the provided content type
     *
     * @param string $contentType
     * @param \DOMNode $node
     * @return StringCastable|NULL
     */
    public function createSerializer(string $contentType, \DOMNode $node);
  }
}
