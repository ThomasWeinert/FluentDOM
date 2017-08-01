<?php

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
