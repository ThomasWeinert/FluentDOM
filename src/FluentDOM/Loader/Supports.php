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
     * Allow the loaders to validate the first char in the provided string.
     *
     * @param string $string
     * @param string $chars
     * @param bool $ignoreWhitespace
     * @return bool
     */
    private function startsWith($string, $chars, $ignoreWhitespace = TRUE) {
      $pattern = $ignoreWhitespace
        ? '(^\s*['.preg_quote($chars).'])'
        : '(^['.preg_quote($chars).'])';
      return (bool)preg_match($pattern, $string);
    }
  }
}