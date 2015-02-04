<?php
/**
 * Serialize a DOM to BadgerFish Json: http://badgerfish.ning.com/
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer\Json {

  use FluentDOM\Serializer\Json;
  use FluentDOM\Xpath;

  /**
   * Serialize a DOM to BadgerFish Json: http://badgerfish.ning.com/
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  class BadgerFish extends Json {

      /**
       * @param \DOMElement $node
       * @return \stdClass
       */
    protected function getNode(\DOMElement $node) {
      $result = new \stdClass();
      $result->{$node->nodeName} = $this->getNodes($node);
      return $result;
    }

    /**
     * @param \DOMElement $node
     * @return \stdClass
     */
    protected function getNodes(\DOMElement $node) {
      $result = new \stdClass();
      $xpath = new Xpath($node->ownerDocument);
      $this->addNamespaces($result, $node, $xpath);
      $this->addAttributes($result, $node, $xpath);
      foreach ($xpath->evaluate('*', $node) as $childNode) {
        $this->addElement($result, $childNode);
      }
      foreach ($xpath->evaluate('text()', $node) as $childNode) {
        $this->addText($result, $childNode);
      }
      return $result;
    }

    /**
     * @param \stdClass $target
     * @param \DOMElement $node
     * @param Xpath $xpath
     */
    protected function addAttributes(\stdClass $target, \DOMElement $node, Xpath $xpath) {
      foreach ($xpath->evaluate('@*', $node) as $attribute) {
        $target->{'@'.$attribute->name} = $attribute->value;
      }
    }

    /**
     * @param \stdClass $target
     * @param \DOMElement $node
     * @param Xpath $xpath
     */
    protected function addNamespaces(\stdClass $target, \DOMElement $node, Xpath $xpath) {
      if ($node->namespaceURI != '' && $node->prefix === '') {
        if (!isset($target->{'@xmlns'})) {
          $target->{'@xmlns'} = new \stdClass();
        }
        $target->{'@xmlns'}->{'$'} = $node->namespaceURI;
      }
      foreach ($xpath->evaluate('namespace::*', $node) as $namespace) {
        if ($namespace->localName === 'xml' || $namespace->localName === 'xmlns') {
          continue;
        }
        if (!isset($target->{'@xmlns'})) {
          $target->{'@xmlns'} = new \stdClass();
        }
        if ($namespace->nodeName !== 'xmlns') {
          $target->{'@xmlns'}->{$namespace->localName} = $namespace->namespaceURI;
        }
      }
    }

    /**
     * @param \stdClass $target
     * @param \DOMElement $node
     */
    private function addElement(\stdClass $target, \DOMElement $node) {
      $nodeName = $node->nodeName;
      if (isset($target->$nodeName)) {
        if (!is_array($target->$nodeName)) {
          $target->{$nodeName} = [$target->{$nodeName}];
        }
        array_push($target->$nodeName, $this->getNodes($node));
      } else {
        $target->$nodeName = $this->getNodes($node);
      }
    }

    /**
     * @param \stdClass $target
     * @param \DOMNode|\DOMText|\DOMCdataSection $node
     */
    private function addText(\stdClass $target, \DOMNode $node) {
      if (!$node->isWhitespaceInElementContent()) {
        if (!isset($target->{'$'})) {
          $target->{'$'} = '';
        }
        $target->{'$'} .= $node->textContent;
      }
    }
  }
}