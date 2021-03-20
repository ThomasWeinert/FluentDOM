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

namespace FluentDOM\Loader\Json {

  use FluentDOM\DOM\Element;
  use FluentDOM\DOM\Implementation;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;

  /**
   * Load a DOM document from a json string or file
   */
  class JsonML implements Loadable {

    use Supports\Json;
    public const CONTENT_TYPES = ['jsonml', 'application/jsonml', 'application/jsonml+json'];

    /**
     * @param \DOMNode|Element $target
     * @param mixed $json
     * @throws UnattachedNode
     */
    public function transferTo(\DOMNode $target, $json): void {
      if (\is_array($json) && \count($json) > 0) {
        $this->transferToElement($target, $json);
      } elseif (\is_scalar($json)) {
        $document = Implementation::getNodeDocument($target);
        $target->appendChild(
          $document->createTextNode($this->getValueAsString($json))
        );
      }
    }

    /**
     * @param Element $node
     * @param \stdClass $properties
     */
    private function addNamespaceAttributes(Element $node, \stdClass $properties): void {
      foreach ($properties as $name => $value) {
        if ($name === 'xmlns' || 0 === \strpos($name, 'xmlns:')) {
          if ($node instanceof \DOMElement) {
            $prefix = $name === 'xmlns' ? NULL : \substr($name, 6);
            if ((string)$node->lookupNamespaceUri($prefix) !== $value) {
              $node->setAttribute($name, $value);
            }
          }
        }
      }
    }

    /**
     * @param Element $node
     * @param \stdClass $properties
     */
    private function addAttributes(Element $node, \stdClass $properties): void {
      $document = $node->ownerDocument;
      foreach ($properties as $name => $value) {
        if (!($name === 'xmlns' || 0 === \strpos($name, 'xmlns:'))) {
          $namespaceURI = $this->getNamespaceForNode($name, $properties, $node);
          $attribute = '' === (string)$namespaceURI
            ? $document->createAttribute($name)
            : $document->createAttributeNS($namespaceURI, $name);
          $attribute->value = $this->getValueAsString($value);
          $node->setAttributeNode($attribute);
        }
      }
    }

    /**
     * @param \DOMNode $node
     * @param mixed $json
     * @throws UnattachedNode
     */
    private function transferToElement(\DOMNode $node, $json): void {
      $document = Implementation::getNodeDocument($node);
      $nodeName = $json[0];
      $length = \count($json);
      $hasProperties = $length > 1 && \is_object($json[1]);
      $properties = $hasProperties ? $json[1] : new \stdClass;
      $namespaceURI = $this->getNamespaceForNode($nodeName, $properties, $node);
      /** @var Element $element */
      $element = '' === (string)$namespaceURI
        ? $document->createElement($nodeName)
        : $document->createElementNS($namespaceURI, $nodeName);
      $node->appendChild($element);
      $this->addNamespaceAttributes($element, $properties);
      $this->addAttributes($element, $properties);
      $childOffset = $hasProperties ? 2 : 1;
      for ($i = $childOffset; $i < $length; $i++) {
        $this->transferTo($element, $json[$i]);
      }
    }
  }
}
