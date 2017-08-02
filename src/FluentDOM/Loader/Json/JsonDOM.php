<?php
/**
 * Load a DOM document from a json string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\DOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Result;
  use FluentDOM\Loader\Supports\Json as SupportsJson;
  use FluentDOM\Utility\QualifiedName;

  /**
   * Load a DOM document from a json string or file
   */
  class JsonDOM implements Loadable {

    use SupportsJson;

    const ON_MAP_KEY = 'onMapKey';

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
    private $_recursions;

    /**
     * Add json:type and json:name attributes to all elements, even if not necessary.
     *
     * @var bool
     */
    private $_verbose;

    /**
     * Called to map key names tag names
     * @var NULL|callable
     */
    private $_onMapKey;

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
    public function __construct(int $options = 0, int $depth = 100) {
      $this->_recursions = $depth;
      $this->_verbose = ($options & self::OPTION_VERBOSE) === self::OPTION_VERBOSE;
    }

    /**
     * @return string[]
     */
    public function getSupported(): array {
      return ['json', 'application/json', 'text/json'];
    }


    /**
     * Load the json string into an DOM Document
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     * @throws \Exception
     * @throws \FluentDOM\Exceptions\InvalidSource
     */
    public function load($source, string $contentType, $options = []) {
      if (FALSE !== ($json = $this->getJson($source, $contentType, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $document->appendChild(
          $root = $document->createElementNS(self::XMLNS, 'json:json')
        );
        $onMapKey = $this->prepareOnMapKey($options);
        $this->transferTo($root, $json, $this->_recursions);
        $this->_onMapKey = $onMapKey;
        return $document;
      }
      return NULL;
    }

    /**
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment($source, string $contentType, $options = []) {
      if ($this->supports($contentType)) {
        $document = new Document('1.0', 'UTF-8');
        $fragment = $document->createDocumentFragment();
        $onMapKey = $this->prepareOnMapKey($options);
        $this->transferTo($fragment, json_decode($source), $this->_recursions);
        $this->_onMapKey = $onMapKey;
        return $fragment;
      }
      return NULL;
    }

    private function prepareOnMapKey($options) {
      $onMapKey = $this->_onMapKey;
      if (isset($options[self::ON_MAP_KEY]) && is_callable($options[self::ON_MAP_KEY])) {
        $this->onMapKey($options[self::ON_MAP_KEY]);
      }
      return $onMapKey;
    }

    /**
     * Get/Set a mapping callback for the tag names. If it is a callable
     * it will be set. FALSE removes the callback.
     *
     * function callback(string $key, bool $isArrayElement) {}
     *
     * @param NULL|FALSE|callable $callback
     * @return callable|NULL|FALSE
     */
    public function onMapKey($callback = NULL) {
      if (NULL !== $callback) {
        $this->_onMapKey = is_callable($callback) ? $callback : NULL;
      }
      return $this->_onMapKey;
    }

    /**
     * Transfer a value into a target xml element node. This sets attributes on the
     * target node and creates child elements for object and array values.
     *
     * If the current element is an object or array the method is called recursive.
     * The $recursions parameter is used to limit the recursion depth of this function.
     *
     * @param \DOMNode|DocumentFragment|Element $target
     * @param mixed $value
     * @param int $recursions
     */
    protected function transferTo(\DOMNode $target, $value, int $recursions = 100) {
      if ($recursions < 1) {
        return;
      }
      if ($target instanceof Element || $target instanceOf DocumentFragment) {
        $type = $this->getTypeFromValue($value);
        switch ($type) {
        case self::TYPE_ARRAY :
          $this->transferArrayTo($target, $value, $this->_recursions - 1);
          break;
        case self::TYPE_OBJECT :
          $this->transferObjectTo($target, $value, $this->_recursions - 1);
          break;
        default :
          if ($target instanceof \DOMElement && ($this->_verbose || $type !== self::TYPE_STRING)) {
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
    public function getTypeFromValue($value): string {
      if (is_array($value)) {
        if (empty($value) || array_keys($value) === range(0, count($value) - 1)) {
          return self::TYPE_ARRAY;
        }
        return self::TYPE_OBJECT;
      }
      if (is_object($value)) {
        return self::TYPE_OBJECT;
      }
      if (NULL === $value) {
        return self::TYPE_NULL;
      }
      if (is_bool($value)) {
        return self::TYPE_BOOLEAN;
      }
      if (is_int($value) || is_float($value)) {
        return self::TYPE_NUMBER;
      }
      return self::TYPE_STRING;
    }

    /**
     * Get a valid qualified name (tag name) using the property name/key.
     *
     * @param string $key
     * @param string $default
     * @param bool $isArrayElement
     * @return string
     */
    private function getQualifiedName(string $key, string $default, bool $isArrayElement = FALSE) {
      if ($callback = $this->onMapKey()) {
        $key = $callback($key, $isArrayElement);
      } elseif ($isArrayElement) {
        $key = $default;
      }
      return QualifiedName::normalizeString($key, self::DEFAULT_QNAME);
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return NULL|string
     */
    public function getValueAsString(string $type, $value) {
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
     * @param \DOMNode|Element|DocumentFragment $target
     * @param array $value
     * @param int $recursions
     */
    private function transferArrayTo(\DOMNode $target, array $value, int $recursions) {
      $parentName = '';
      if ($target instanceof Element) {
        $target->setAttributeNS(self::XMLNS, 'json:type', 'array');
        $parentName = $target->getAttributeNS(self::XMLNS, 'name') ?: $target->localName;
      }
      foreach ($value as $item) {
        $child = $target->appendChild(
          $target->ownerDocument->createElement(
            $this->getQualifiedName($parentName, self::DEFAULT_QNAME, TRUE
            )
          )
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
     * @param \DOMNode|Element|DocumentFragment $target
     * @param mixed $value
     * @param int $recursions
     */
    private function transferObjectTo(\DOMNode $target, $value, int $recursions) {
      $properties = is_array($value) ? $value : get_object_vars($value);
      if ($this->_verbose || empty($properties)) {
        $target->setAttributeNS(self::XMLNS, 'json:type', 'object');
      }
      foreach ($properties as $property => $item) {
        $qname = $this->getQualifiedName($property, self::DEFAULT_QNAME);
        $target->appendChild(
          $child = $target->ownerDocument->createElement($qname)
        );
        if ($this->_verbose || $qname !== $property) {
          $child->setAttributeNS(self::XMLNS, 'json:name', $property);
        }
        $this->transferTo($child, $item, $recursions);
      }
    }
  }
}