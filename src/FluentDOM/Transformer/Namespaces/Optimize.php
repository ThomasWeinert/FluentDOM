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

namespace FluentDOM\Transformer\Namespaces {

  use FluentDOM\DOM\Implementation;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Transformer\Namespaces;

  class Optimize extends Namespaces {

    /**
     * @var array
     */
    private $_namespaceUris;

    /**
     * Create a namespace optimizer for the provided document/node. The provided
     * document will be copied.
     *
     * The second argument allows to provide namespaces and prefixes. The
     * keys of the array are the namespace uri, the values are the prefixes.
     *
     * If a namespace is not provided it is read from the source node.
     *
     * You can use the same prefix for multiple namespace uris. Empty prefixes
     * are possible (default namespace for an element).
     *
     * It is highly recommend that you always use a non-empty prefix if the
     * here are attributes in that namespace. Attributes always need a prefix
     * to make use of the namespace.
     *
     * @param \DOMNode $node
     * @param array $namespaces
     * @throws UnattachedNode
     */
    public function __construct(\DOMNode $node, array $namespaces = []) {
      parent::__construct($node);
      $this->_namespaceUris = $namespaces;
    }

    /**
     * Add a node to the target element, just copies any child nodes
     * except elements. Element nodes are recreated with mapped/optimized
     * namespaces.
     *
     * @param \DOMNode $target
     * @param \DOMNode $source
     * @throws UnattachedNode
     */
    protected function addNode(\DOMNode $target, \DOMNode $source): void {
      if ($source instanceof \DOMElement) {
        $this->addElement($target, $source);
      } else {
        $document = Implementation::getNodeDocument($target);
        $target->appendChild($document->importNode($source));
      }
    }

    /**
     * Add an element node to the target (document or element)
     *
     * Namespaces are mapped and added to the mote remote ancestor possible.
     *
     * @param \DOMNode $target
     * @param \DOMElement $source
     * @throws UnattachedNode
     */
    private function addElement(\DOMNode $target, \DOMElement $source): void {
      [$prefix, $name, $uri] = $this->getNodeDefinition($source);
      if ($target instanceof \DOMElement) {
        $this->addNamespaceAttribute($target, $prefix, $uri);
      }
      $newNode = $this->createElement($target, $prefix, $name, $uri);
      foreach ($source->attributes as $attribute) {
        $this->addAttribute($newNode, $attribute);
      }
      foreach ($source->childNodes as $childNode) {
        $this->addNode($newNode, $childNode);
      }
    }

    /**
     * @param \DOMNode $target
     * @param string $prefix
     * @param string $name
     * @param string $namespaceURI
     * @return \DOMElement
     * @throws UnattachedNode
     */
    private function createElement(\DOMNode $target, string $prefix, string $name, string $namespaceURI): \DOMElement {
      $document = Implementation::getNodeDocument($target);
      $newNodeName = empty($prefix) ? $name : $prefix.':'.$name;
      if (empty($namespaceURI) && NULL === $target->lookupNamespaceUri(NULL)) {
        $newNode = $document->createElement($newNodeName);
      } else {
        $newNode = $document->createElementNS($namespaceURI, $newNodeName);
      }
      $target->appendChild($newNode);
      return $newNode;
    }

    /**
     * Add an attribute to the target element node.
     *
     * @param \DOMElement $target
     * @param \DOMAttr $source
     */
    private function addAttribute(\DOMElement $target, \DOMAttr $source): void {
      [$prefix, $name, $uri] = $this->getNodeDefinition($source);
      if (empty($prefix)) {
        $target->setAttribute($name, $source->value);
      } else {
        $target->setAttributeNS(
          $uri, $prefix.':'.$name, $source->value
        );
      }
    }

    /**
     * Get the node name definition (prefix, namespace, local name) for
     * the target node
     *
     * @param \DOMNode $node
     * @return array
     */
    private function getNodeDefinition(\DOMNode $node): array {
      $isElement = $node instanceof \DOMElement;
      $prefix = $isElement && $node->prefix === 'default' ? NULL : $node->prefix;
      $name = $node->localName;
      $uri = (string)$node->namespaceURI;
      $targetUri = $this->_namespaceUris[$uri] ?? '';
      if (
        $targetUri !== '#default' &&
        (
          $targetUri !== '' ||
          ($isElement && isset($this->_namespaceUris[$uri]))
        )
      ) {
        $prefix = $this->_namespaceUris[$uri];
      }
      return [
        (string)$prefix, $name, $uri
      ];
    }

    /**
     * @param \DOMElement $node
     * @param string $prefix
     * @param string $namespaceURI
     */
    private function addNamespaceAttribute(\DOMElement $node, string $prefix, string $namespaceURI): void {
      if (
        ($node->parentNode instanceof \DOMElement) &&
        $this->canAddNamespaceToNode($node->parentNode, $prefix, $namespaceURI)
      ) {
        $this->addNamespaceAttribute($node->parentNode, $prefix, $namespaceURI);
      }
      if ($this->canAddNamespaceToNode($node, $prefix, $namespaceURI)) {
        $attributeName = '' === $prefix ? 'xmlns' : 'xmlns:'.$prefix;
        if (!$node->hasAttribute($attributeName)) {
          $node->setAttribute($attributeName, $namespaceURI);
        }
      }
    }

    /**
     * @param \DOMNode $node
     * @param string $prefix
     * @param string $namespaceURI
     * @return bool
     */
    private function canAddNamespaceToNode(\DOMNode $node, string $prefix, string $namespaceURI): bool {
      $currentUri = (string)$node->lookupNamespaceUri($prefix === '' ? NULL : $prefix);
      $hasNoNamespace = empty($node->namespaceURI);
      if ($hasNoNamespace && $prefix === '') {
        return FALSE;
      }
      if ($currentUri === '') {
        return ($currentUri !== $namespaceURI);
      }
      return FALSE;
    }
  }
}
