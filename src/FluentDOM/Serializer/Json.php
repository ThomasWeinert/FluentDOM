<?php
/**
 * Abstract superclass for json serializers (serialize a DOM into a Json structure).
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer {

  /**
   * Abstract superclass for json serializers (serialize a DOM into a Json structure).
   *
   * See http://wiki.open311.org/index.php?title=JSON_and_XML_Conversion for
   * different formats.
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  abstract class Json implements \JsonSerializable {

    /**
     * @var \DOMDocument
     */
    protected $_document = NULL;

    /**
     * @var int
     */
    private $_options = 0;

    /**
     * @var int
     */
    private $_depth = 512;

    /**
     * @param \DOMDocument $document
     * @param int $options
     * @param int $depth
     */
    public function __construct(\DOMDocument $document, $options = 0, $depth = 512) {
      $this->_document = $document;
      $this->_options = (int)$options;
      $this->_depth = (int)$depth;
    }

    /**
     * @return string
     */
    public function __toString() {
      $json = version_compare(PHP_VERSION, '5.5.0', '>=')
        ? json_encode($this, $this->_options, $this->_depth)
        : json_encode($this, $this->_options);
      return ($json) ? $json : '';
    }

    /**
     * @return array|NULL
     */
    public function jsonSerialize() {
      if (isset($this->_document->documentElement)) {
        return $this->getNode($this->_document->documentElement);
      }
      return $this->getEmpty();
    }

    /**
     * @return mixed
     */
    protected function getEmpty() {
      return new \stdClass();
    }

    /**
     * @return array|\stdClass|NULL
     */
    abstract protected function getNode(\DOMElement $node);
  }
}