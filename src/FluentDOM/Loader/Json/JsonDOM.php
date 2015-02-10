<?php
/**
 * Load a DOM document from a json string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\Document;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;
  use FluentDOM\QualifiedName;

  /**
   * Load a DOM document from a json string or file
   */
  class JsonDOM implements Loadable {

    use Supports\Json;

    const XMLNS = 'urn:carica-json-dom.2013';
    const DEFAULT_QNAME = '_';

    const OPTION_VERBOSE = 1;

    const TYPE_NULL = 'null';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY = 'array';

    /**
     * Maximum recursions
     *
     * @var int
     */
    private $_recursions = 100;

    /**
     * Add json:type and json:name attributes to all elements, even if not necessary.
     *
     * @var bool
     */
    private $_verbose = FALSE;

    /**
     * Create the loader for a json string.
     *
     * The string will be decoded into a php variable structure and convert into a DOM document
     * If options contains is self::OPTION_VERBOSE, the DOMNodes will all have
     * json:type and json:name attributes. Even if the information could be read from the structure.
     *
     * @param int $options
     * @param int $depth
     */
    public function __construct($options = 0, $depth = 100) {
      $this->_recursions = (int)$depth;
      $this->_verbose = ($options & self::OPTION_VERBOSE) == self::OPTION_VERBOSE;
    }

    /**
     * @return string[]
     */
    public function getSupported() {
      return ['json', 'application/json', 'text/json'];
    }


    /**
     * Load the json string into an DOMDocument
     *
     * @param mixed $source
     * @param string $contentType
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if (FALSE !== ($json = $this->getJson($source, $contentType))) {
        $dom = new Document('1.0', 'UTF-8');
        $dom->appendChild(
          $root = $dom->createElementNS(self::XMLNS, 'json:json')
        );
        $this->transferTo($root, $json, $this->_recursions);
        return $dom;
      }
      return NULL;
    }

    /**
     * Transfer a value into a target xml element node. This sets attributes on the
     * target node and creates child elements for object and array values.
     *
     * If the current element is an object or array the method is called recursive.
     * The $recursions parameter is used to limit the recursion depth of this function.
     *
     * @param \DOMElement|\DOMNode $target
     * @param $value
     * @param int $recursions
     */
    protected function transferTo(\DOMNode $target, $value, $recursions = 100) {
      if ($recursions < 1) {
        return;
      } elseif ($target instanceof \DOMElement) {
        $type = $this->getTypeFromValue($value);
        switch ($type) {
        case self::TYPE_ARRAY :
          $this->transferArrayTo($target, $value, $this->_recursions - 1);
          break;
        case self::TYPE_OBJECT :
          $this->transferObjectTo($target, $value, $this->_recursions - 1);
          break;
        default :
          if ($this->_verbose || $type != self::TYPE_STRING) {
            $target->setAttributeNS(self::XMLNS, 'json:type', $type);
          }
          $string = $this->getValueAsString($type, $value);
          if (is_string($string)) {
            $target->appendChild($target->ownerDocument->createTextNode($string));
          }
        }
      }
    }

    /**
     * Get the type from a variable value.
     *
     * @param mixed $value
     * @return string
     */
    public function getTypeFromValue($value) {
      if (is_array($value)) {
        if (empty($value) || array_keys($value) === range(0, count($value) - 1)) {
          return self::TYPE_ARRAY;
        }
        return self::TYPE_OBJECT;
      } elseif (is_object($value)) {
        return self::TYPE_OBJECT;
      } elseif (NULL === $value) {
        return self::TYPE_NULL;
      } elseif (is_bool($value)) {
        return self::TYPE_BOOLEAN;
      } elseif (is_int($value) || is_float($value)) {
        return self::TYPE_NUMBER;
      } else {
        return self::TYPE_STRING;
      }
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return null|string
     */
    public function getValueAsString($type, $value) {
      switch ($type) {
      case self::TYPE_NULL :
        return NULL;
      case self::TYPE_BOOLEAN :
        return $value ? 'true' : 'false';
      default :
        return (string)$value;
      }
    }

    /**
     * Transfer an array value into a target element node. Sets the json:type attribute to 'array' and
     * creates child element nodes for each array element using the default QName.
     *
     * @param \DOMElement $target
     * @param array $value
     * @param int $recursions
     */
    private function transferArrayTo(\DOMElement $target, array $value, $recursions) {
      $target->setAttributeNS(self::XMLNS, 'json:type', 'array');
      foreach ($value as $item) {
        $target->appendChild(
          $child = $target->ownerDocument->createElement(self::DEFAULT_QNAME)
        );
        $this->transferTo($child, $item, $recursions);
      }
    }

    /**
     * Transfer an object value into a target element node. If the object has no properties,
     * the json:type attribute is always set to 'object'. If verbose is not set the json:type attribute will
     * be omitted if the object value has properties.
     *
     * The method creates child nodes for each property. The property name will be normalized to a valid NCName.
     * If the normalized NCName is different from the property name or verbose is TRUE, a json:name attribute
     * with the property name will be added.
     *
     * @param \DOMElement $target
     * @param object $value
     * @param int $recursions
     */
    private function transferObjectTo(\DOMElement $target, $value, $recursions) {
      $properties = is_array($value) ? $value : get_object_vars($value);
      if ($this->_verbose || empty($properties)) {
        $target->setAttributeNS(self::XMLNS, 'json:type', 'object');
      }
      foreach ($properties as $property => $item) {
        $qname = QualifiedName::normalizeString($property, self::DEFAULT_QNAME);
        $target->appendChild(
          $child = $target->ownerDocument->createElement($qname)
        );
        if ($this->_verbose || $qname != $property) {
          $child->setAttributeNS(self::XMLNS, 'json:name', $property);
        }
        $this->transferTo($child, $item, $recursions);
      }
    }
  }
}