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

namespace FluentDOM\Serializer\Json {

  use FluentDOM\DOM\Xpath;
  use FluentDOM\Serializer\Json;

  /**
   * Serialize a DOM to BadgerFish Json: http://badgerfish.ning.com/
   */
  class BadgerFish extends Json {

    /**
     * @param \DOMElement $node
     * @return mixed
     */
    protected function getNode(\DOMElement $node) {
      $result = new \stdClass();
      $result->{$node->nodeName} = $this->getNodes($node);
      return $result;
    }

    /**
     * @param \DOMElement $node
     * @return mixed
     */
    protected function getNodes(\DOMElement $node) {
      $result = new \stdClass();
      $xpath = new Xpath($node->ownerDocument);
      $this->addNamespaces($result, $node, $xpath);
      $this->addAttributes($result, $node, $xpath);
      $nodes = $xpath->evaluate('*|text()', $node);
      foreach ($nodes as $childNode) {
        if ($childNode instanceof \DOMElement) {
          $this->addElement($result, $childNode);
        } else {
          $this->addText($result, $childNode);
        }
      }
      return $result;
    }

    /**
     * @param \stdClass $target
     * @param \DOMElement $node
     * @param Xpath $xpath
     */
    protected function addAttributes(\stdClass $target, \DOMElement $node, Xpath $xpath): void {
      $nodes = $xpath->evaluate('@*', $node);
      foreach ($nodes as $attribute) {
        $target->{'@'.$attribute->name} = $attribute->value;
      }
    }

    /**
     * @param \stdClass $target
     * @param \DOMElement $node
     * @param Xpath $xpath
     */
    protected function addNamespaces(\stdClass $target, \DOMElement $node, Xpath $xpath): void {
      if ((string)$node->namespaceURI !== '' && $node->prefix === '') {
        if (!isset($target->{'@xmlns'})) {
          $target->{'@xmlns'} = new \stdClass();
        }
        $target->{'@xmlns'}->{'$'} = $node->namespaceURI;
      }
      $nodes = $xpath->evaluate('namespace::*', $node);
      foreach ($nodes as $namespaceNode) {
        if ($namespaceNode->localName === 'xml' || $namespaceNode->localName === 'xmlns') {
          continue;
        }
        if (!isset($target->{'@xmlns'})) {
          $target->{'@xmlns'} = new \stdClass();
        }
        if ($namespaceNode->nodeName !== 'xmlns') {
          $target->{'@xmlns'}->{$namespaceNode->localName} = $namespaceNode->namespaceURI;
        }
      }
    }

    /**
     * @param \stdClass $target
     * @param \DOMElement $node
     */
    private function addElement(\stdClass $target, \DOMElement $node): void {
      $nodeName = $node->nodeName;
      if (isset($target->$nodeName)) {
        if (!is_array($target->$nodeName)) {
          $target->{$nodeName} = [$target->{$nodeName}];
        }
        $target->{$nodeName}[] = $this->getNodes($node);
      } else {
        $target->$nodeName = $this->getNodes($node);
      }
    }

    /**
     * @param \stdClass $target
     * @param \DOMNode|\DOMText|\DOMCdataSection $node
     */
    private function addText(\stdClass $target, \DOMNode $node): void {
      if (!($node instanceof \DOMText) || !$node->isWhitespaceInElementContent()) {
        if (!isset($target->{'$'})) {
          $target->{'$'} = '';
        }
        $target->{'$'} .= $node->textContent;
      }
    }
  }
}
