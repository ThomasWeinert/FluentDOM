<?php
/**
 * Serialize a DOM to RabbitFish Json: http://www.bramstein.com/projects/xsltjson/
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer\Json {

  use FluentDOM\XPath;

  /**
   * Serialize a DOM to RabbitFish Json: http://www.bramstein.com/projects/xsltjson/
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  class RabbitFish extends BadgerFish {

    /**
     * @param \DOMElement $node
     * @return \stdClass
     */
    protected function getNodes(\DOMElement $node) {
      $xpath = new XPath($node->ownerDocument);
      $hasText = $xpath->evaluate('count(text()[normalize-space(.) != ""]) > 0', $node);
      $hasElements = $xpath->evaluate('count(*) > 0', $node);
      $attributes = new \stdClass();
      $this->addAttributes($attributes, $node, $xpath);
      $this->addNamespaces($attributes, $node, $xpath);
      $attributes = (array)$attributes;
      if ($hasText && $hasElements) {
        $result = [];
        foreach ($attributes as $name => $value) {
          $child = new \stdClass();
          $child->{$name} = $value;
          $result[] = $child;
        }
        foreach ($xpath->evaluate('*|text()[normalize-space(.) != ""]', $node) as $childNode) {
          /** @var \DOMElement|\DOMText|\DOMCdataSection $childNode */
          if ($childNode instanceof \DOMElement) {
            $child = new \stdClass();
            $child->{$childNode->nodeName} = $this->getNodes($childNode);
            $result[] = $child;
          } elseif (!$childNode->isWhitespaceInElementContent()) {
            $result[] = $childNode->nodeValue;
          }
        }
        return $result;
      } elseif ($hasText && count($attributes) == 0) {
        return $node->nodeValue;
      } else {
        return parent::getNodes($node);
      }
    }
  }
}