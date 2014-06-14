<?php

namespace FluentDOM\Loader {

  trait Supports {

    /**
     * @see Loadable::supports
     * @param string $contentType
     * @return bool
     */
    public function supports($contentType) {
      return (in_array($contentType, $this->getSupported()));
    }

    /**
     * @return array
     */
    public function getSupported() {
      return isset($this->_supportedTypes) ? $this->_supportedTypes : array();
    }
  }
}