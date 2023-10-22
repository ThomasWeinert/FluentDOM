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

namespace FluentDOM\Serializer\Json {

  use FluentDOM\DOM\Xpath;

  /**
   * Serialize a DOM to RabbitFish JsonSerializer: http://www.bramstein.com/projects/xsltjson/
   */
  class RabbitFishSerializer extends BadgerFishSerializer {

    /**
     * @param \DOMElement $node
     * @return mixed
     */
    protected function getNodes(\DOMElement $node): mixed {
      $xpath = new Xpath($node->ownerDocument);
      $hasText = $xpath->evaluate('count(text()[normalize-space(.) != ""]) > 0', $node);
      $hasElements = $xpath->evaluate('count(*) > 0', $node);
      $attributes = new \stdClass();
      $this->addAttributes($attributes, $node, $xpath);
      $this->addNamespaces($attributes, $node, $xpath);
      $attributes = (array)$attributes;
      if ($hasText && $hasElements) {
        return $this->getNodesArray($node, $attributes, $xpath);
      }
      if ($hasText && count($attributes) === 0) {
        return $node->nodeValue;
      }
      return parent::getNodes($node);
    }

    private function getNodesArray(
      \DOMElement $node, \stdClass|array $attributes, Xpath $xpath
    ): array {
      $result = [];
      foreach ((array)$attributes as $name => $value) {
        $child = new \stdClass();
        $child->{$name} = $value;
        $result[] = $child;
      }
      $nodes = $xpath->evaluate('*|text()[normalize-space(.) != ""]', $node);
      foreach ($nodes as $childNode) {
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
    }
  }
}
