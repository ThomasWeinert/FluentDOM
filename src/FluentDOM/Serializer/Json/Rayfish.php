<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Serializer\Json {

  use FluentDOM\Serializer\Json;

  /**
   * Serialize a DOM to Rayfish Json: http://www.bramstein.com/projects/xsltjson/
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
   */
  class Rayfish extends Json {

    /**
     * @param \DOMElement $node
     * @return mixed
     */
    protected function getNode(\DOMElement $node) {
      $result = new \stdClass();
      $result->{'#name'} = $node->nodeName;
      $result->{'#text'} = '';
      $result->{'#children'} = \array_merge(
        $this->getNamespaces($node),
        $this->getAttributes($node)
      );
      foreach ($node->childNodes as $childNode) {
        if ($childNode instanceof \DOMElement) {
          $result->{'#children'}[] = $this->getNode($childNode);
        } elseif (
          ($childNode instanceof \DOMText || $childNode instanceof \DOMCdataSection) &&
          !$childNode->isWhitespaceInElementContent()
        ) {
          $result->{'#text'} .= $childNode->textContent;
        }
      }
      if (empty($result->{'#text'})) {
        $result->{'#text'} = NULL;
      }
      return $result;
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    private function getAttributes(\DOMElement $node): array {
      $result = [];
      foreach ($node->attributes as $name => $attributeNode) {
        $attribute = new \stdClass();
        $attribute->{'#name'} = '@'.$attributeNode->name;
        $attribute->{'#text'} = $attributeNode->value;
        $attribute->{'#children'} = [];
        $result[] = $attribute;
      }
      return $result;
    }

    protected function getNamespaces(\DOMElement $node): array {
      $result = [];
      foreach (parent::getNamespaces($node) as $prefix => $uri) {
        $attribute = new \stdClass();
        $attribute->{'#name'} = '@'.$prefix;
        $attribute->{'#text'} = $uri;
        $attribute->{'#children'} = [];
        $result[] = $attribute;
      }
      return $result;
    }
  }
}
