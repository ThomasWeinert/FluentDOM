<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader\Json {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\DOM\Element;
  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Result;
  use FluentDOM\Loader\Supports\Json as SupportsJson;
  use FluentDOM\Utility\Constraints;
  use FluentDOM\Utility\QualifiedName;
  use FluentDOM\Utility\JsonValueType;

  /**
   * Load a DOM document from a json string or file
   */
  class JsonDOM implements Loadable {

    use SupportsJson;

    public const XMLNS = 'urn:carica-json-dom.2013';

    private const DEFAULT_QNAME = '_';

    public const CONTENT_TYPES = ['json', 'application/json', 'text/json'];

    public const ON_MAP_KEY = 'onMapKey';
    public const OPTION_VERBOSE = 1;

    /**
     * Maximum recursions
     */
    private int $_recursions;

    /**
     * Add json:type and json:name attributes to all elements, even if not necessary.
     */
    private bool $_verbose;

    /**
     * Called to map key names tag names
     */
    private ?\Closure $_onMapKey = NULL;

    /**
     * Create the loader for a json string.
     *
     * The string will be decoded into a php variable structure and convert into a DOM document
     * If options contains is self::OPTION_VERBOSE, the DOMNodes will all have
     * json:type and json:name attributes. Even if the information could be read from the structure.
     */
    public function __construct(int $options = 0, int $depth = 100) {
      $this->_recursions = $depth;
      $this->_verbose = ($options & self::OPTION_VERBOSE) === self::OPTION_VERBOSE;
    }

    /**
     * Load the json string into an DOM Document
     *
     * @throws \Exception
     * @throws InvalidSource
     */
    public function load(mixed $source, string $contentType, iterable $options = []): ?Result {
      if (FALSE !== ($json = $this->getJson($source, $contentType, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $document->appendChild(
          $root = $document->createElementNS(self::XMLNS, 'json:json')
        );
        $onMapKey = $this->prepareOnMapKey($options);
        $this->transferTo($root, $json, $this->_recursions);
        $this->_onMapKey = $onMapKey;
        return new Result($document, $contentType);
      }
      return NULL;
    }

    /**
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment(mixed $source, string $contentType, iterable $options = []): ?DocumentFragment {
      if ($this->supports($contentType)) {
        $document = new Document('1.0', 'UTF-8');
        $fragment = $document->createDocumentFragment();
        $onMapKey = $this->prepareOnMapKey($options);
        $this->transferTo($fragment, \json_decode($source, FALSE), $this->_recursions);
        $this->_onMapKey = $onMapKey;
        return $fragment;
      }
      return NULL;
    }

    private function prepareOnMapKey($options): ?callable {
      $onMapKey = $this->_onMapKey;
      if (isset($options[self::ON_MAP_KEY]) && \is_callable($options[self::ON_MAP_KEY])) {
        $this->onMapKey($options[self::ON_MAP_KEY]);
      }
      return $onMapKey;
    }

    /**
     * Get/Set a mapping callback for the tag names. If it is a callable
     * it will be set. FALSE removes the callback.
     *
     * function callback(string $key, bool $isArrayElement) {}
     */
    public function onMapKey(callable $callback = NULL): ?callable {
      if (NULL !== $callback) {
        $this->_onMapKey = Constraints::filterCallable($callback);
      }
      return $this->_onMapKey;
    }

    /**
     * Transfer a value into a target xml element node. This sets attributes on the
     * target node and creates child elements for object and array values.
     *
     * If the current element is an object or array the method is called recursive.
     * The $recursions parameter is used to limit the recursion depth of this function.
     */
    protected function transferTo(\DOMNode $target, mixed $json, int $recursions = 100): void {
      if ($recursions < 1) {
        return;
      }
      if ($target instanceof Element || $target instanceOf DocumentFragment) {
        $type = JsonValueType::getTypeFromValue($json);
        switch ($type) {
        case JsonValueType::TYPE_ARRAY :
          $this->transferArrayTo($target, $json, $recursions - 1);
          break;
        case JsonValueType::TYPE_OBJECT :
          $this->transferObjectTo($target, $json, $recursions - 1);
          break;
        default :
          if ($target instanceof \DOMElement && ($this->_verbose || $type !== JsonValueType::TYPE_STRING)) {
            $target->setAttributeNS(self::XMLNS, 'json:type', $type);
          }
          $string = $this->getValueAsString($json, $type);
          if (\is_string($string)) {
            $target->appendChild($target->ownerDocument->createTextNode($string));
          }
        }
      }
    }

    /**
     * Get a valid qualified name (tag name) using the property name/key.
     *
     * @param string $key
     * @param string $default
     * @param bool $isArrayElement
     * @return string
     */
    private function getQualifiedName(string $key, string $default, bool $isArrayElement = FALSE): string {
      if ($callback = $this->onMapKey()) {
        $key = $callback($key, $isArrayElement);
      } elseif ($isArrayElement) {
        $key = $default;
      }
      return QualifiedName::normalizeString($key, self::DEFAULT_QNAME);
    }

    private function getValueAsString(mixed $value, string $type): ?string {
      return match ($type) {
        JsonValueType::TYPE_NULL => NULL,
        JsonValueType::TYPE_BOOLEAN => $value ? 'true' : 'false',
        default => (string)$value,
      };
    }

    /**
     * Transfer an array value into a target element node. Sets the json:type attribute to 'array' and
     * creates child element nodes for each array element using the default QName.
     * @throws \DOMException
     */
    private function transferArrayTo(\DOMNode $target, array $value, int $recursions): void {
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
     */
    private function transferObjectTo(\DOMNode $target, mixed $value, int $recursions): void {
      $properties = \is_array($value) ? $value : \get_object_vars($value);
      if (($this->_verbose || empty($properties)) && $target instanceof \DOMElement) {
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
