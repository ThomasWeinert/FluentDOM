<?php

namespace FluentDOM\Loader {

  trait Supports {

    /**
     * @see Loadable::supports
     * @param string $contentType
     * @return bool
     */
    public function supports($contentType) {
      return (in_array(strtolower($contentType), $this->getSupported()));
    }

    /**
     * @return string[]
     */
    public function getSupported() {
      return array();
    }

    /**
     * Allow the loaders to validate the first part of the provided string.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $ignoreWhitespace
     * @return bool
     */
    private function startsWith($haystack, $needle, $ignoreWhitespace = TRUE) {
      $pattern = $ignoreWhitespace
        ? '(^\s*'.preg_quote($needle).')'
        : '(^'.preg_quote($needle).')';
      return (bool)preg_match($pattern, $haystack);
    }
  }
}