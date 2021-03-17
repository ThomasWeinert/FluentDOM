<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader\XDM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\DOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Result;
  use FluentDOM\Loader\Supports\Json as SupportsJson;

  /**
   * Load a XDM (Xpath Data Model) document from a json string or file
   */
  class JsonAsXDM implements Loadable {

    use SupportsJson;

    const CONTENT_TYPES = ['xdm-json', 'application/xdm-json', 'text/xdm-json'];
    const XMLNS_FN = 'http://www.w3.org/2005/xpath-functions';

    const TYPE_NULL = 'null';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_OBJECT = 'map';
    const TYPE_ARRAY = 'array';

    /**
     * Maximum recursions
     *
     * @var int
     */
    private $_recursions;

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
    }

    /**
     * @return string[]
     */
    public function getSupported(): array {
      return self::CONTENT_TYPES;
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
        $this->transferTo($document, $json, NULL, $this->_recursions);
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
        $this->transferTo($fragment, \json_decode($source), NULL, $this->_recursions);
        return $fragment;
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
     * @param \DOMNode|DocumentFragment|Element $parent
     * @param mixed $value
     * @param string|null $key
     * @param int $recursions
     */
    protected function transferTo(\DOMNode $parent, $value, string $key = NULL, int $recursions = 100) {
      if ($recursions < 1) {
        return;
      }
      $document = $parent instanceof Document ? $parent : $parent->ownerDocument;
      if (
        $document instanceof Document &&
        (
          $parent instanceof Document || $parent instanceof Element || $parent instanceOf DocumentFragment
        )
      ) {
        $type = $this->getTypeFromValue($value);
        $parent->appendChild(
          $target = $document->createElementNS(self::XMLNS_FN, $type)
        );
        if (NULL !== $key) {
          $target->setAttribute('key', $key);
        }
        switch ($type) {
        case self::TYPE_ARRAY :
          foreach ($value as $childValue) {
            $this->transferTo($target, $childValue, NULL, $recursions - 1);
          }
          break;
        case self::TYPE_OBJECT :
          $properties = \is_array($value) ? $value : \get_object_vars($value);
          foreach ($properties as $childKey => $childValue) {
            $this->transferTo($target, $childValue, $childKey, $recursions - 1);
          }
          break;
        default :
          $string = $this->getValueAsString($type, $value);
          if (\is_string($string)) {
            $target->appendChild($document->createTextNode($string));
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
      if (\is_array($value)) {
        if (empty($value) || \array_keys($value) === \range(0, \count($value) - 1)) {
          return self::TYPE_ARRAY;
        }
        return self::TYPE_OBJECT;
      }
      if (\is_object($value)) {
        return self::TYPE_OBJECT;
      }
      if (NULL === $value) {
        return self::TYPE_NULL;
      }
      if (\is_bool($value)) {
        return self::TYPE_BOOLEAN;
      }
      if (\is_int($value) || \is_float($value)) {
        return self::TYPE_NUMBER;
      }
      return self::TYPE_STRING;
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
  }
}
