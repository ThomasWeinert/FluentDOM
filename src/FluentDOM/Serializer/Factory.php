<?php

namespace FluentDOM\Serializer {

  interface Factory {

    /**
     * Return a serializer for the provided content type
     *
     * @param string $contentType
     * @param \DOMNode $node
     * @return object|null
     */
    function createSerializer($contentType, \DOMNode $node);
  }
}
