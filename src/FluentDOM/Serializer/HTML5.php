<?php
/**
 * Serialize an (XHTML) DOM into a HTML5 string.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer {

  use Masterminds\HTML5 as HTML5Support;

  /**
   * Serialize an (XHTML) DOM into a HTML5 string.
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  class Html5 {

    /**
     * @var \DOMDocument
     */
    private $_document = NULL;

    /**
     * @var array
     */
    private $_options = [];

    public function __construct(\DOMDocument $document, array $options = []) {
      if (!class_exists('Masterminds\\HTML5')) {
        throw new \LogicException(
          'HTML5 support missing please install the "masterminds/html5" package'
        );
      }
      $this->_document = $document;
      $this->_options = $options;
    }

    public function __toString() {
      try {
        return $this->asString();
      } catch (\Exception $e) {
        return '';
      }
    }

    public function asString() {
      $html5 = new HTML5Support($this->_options);
      return (string)$html5->saveHTML($this->_document);
    }
  }
}
