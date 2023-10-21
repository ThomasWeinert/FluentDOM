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

namespace FluentDOM\Loader\XDM {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\DOM\Element;
  use FluentDOM\DOM\Implementation;
  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Result;
  use FluentDOM\Loader\Supports\Json as SupportsJson;
  use FluentDOM\Utility\JsonValueType;

  /**
   * Load a XDM (Xpath Data Model) document from a json string or file
   */
  class JsonAsXDM implements Loadable {

    use SupportsJson;

    public const CONTENT_TYPES = ['xdm-json', 'application/xdm-json', 'text/xdm-json'];
    private const XMLNS_FN = 'http://www.w3.org/2005/xpath-functions';

    /**
     * Maximum recursions
     */
    private int $_recursions;

    /**
     * Create the loader for a json string.
     *
     * The string will be decoded into a php variable structure and convert into a DOM document
     * If options contains is self::OPTION_VERBOSE, the DOMNodes will all have
     * json:type and json:name attributes. Even if the information could be read from the structure.
     *
     * @noinspection PhpUnusedParameterInspection
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
     * @throws InvalidSource|UnattachedNode
     * @throws \DOMException
     */
    public function load(
      mixed $source, string $contentType, iterable $options = []
    ): ?Result {
      if (FALSE !== ($json = $this->getJson($source, $contentType, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $this->transferTo($document, $json, NULL, $this->_recursions);
        return new Result($document, $contentType);
      }
      return NULL;
    }

    /**
     * @throws UnattachedNode
     * @throws \DOMException
     */
    public function loadFragment(
      mixed $source, string $contentType, iterable $options = []
    ): ?DocumentFragment {
      if ($this->supports($contentType)) {
        $document = new Document('1.0', 'UTF-8');
        $fragment = $document->createDocumentFragment();
        $this->transferTo($fragment, \json_decode($source, FALSE), NULL, $this->_recursions);
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
     * @throws UnattachedNode|\DOMException
     */
    protected function transferTo(
      \DOMNode $target, mixed $json, string $key = NULL, int $recursions = 100
    ): void {
      if ($recursions < 1) {
        return;
      }
      $document = Implementation::getNodeDocument($target);
      if (
        $document instanceof Document &&
        (
          $target instanceof Document ||
          $target instanceof Element ||
          $target instanceOf DocumentFragment
        )
      ) {
        $type = JsonValueType::getTypeFromValue($json);
        $target->appendChild(
          $element = $document->createElementNS(
            self::XMLNS_FN, $this->getNameForType($type)
          )
        );
        if (NULL !== $key) {
          $element->setAttribute('key', $key);
        }
        switch ($type) {
        case JsonValueType::TYPE_ARRAY :
          foreach ($json as $childValue) {
            $this->transferTo($element, $childValue, NULL, $recursions - 1);
          }
          break;
        case JsonValueType::TYPE_OBJECT :
          $properties = \is_array($json) ? $json : \get_object_vars($json);
          foreach ($properties as $childKey => $childValue) {
            $this->transferTo($element, $childValue, $childKey, $recursions - 1);
          }
          break;
        default :
          $string = $this->getValueAsString($json, $type);
          if (\is_string($string)) {
            $element->appendChild($document->createTextNode($string));
          }
        }
      }
    }

    private function getValueAsString(mixed $value, string $type): ?string {
      return match ($type) {
        JsonValueType::TYPE_NULL => NULL,
        JsonValueType::TYPE_BOOLEAN => $value ? 'true' : 'false',
        default => (string)$value,
      };
    }

    private function getNameForType(string $type): string {
      return match ($type) {
        JsonValueType::TYPE_OBJECT => 'map',
        default => $type,
      };
    }
  }
}
