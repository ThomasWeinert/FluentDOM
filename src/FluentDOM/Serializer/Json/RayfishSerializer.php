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

  use FluentDOM\Serializer\JsonSerializer;

  /**
   * Serialize a DOM to RayfishLoader JsonSerializer: http://www.bramstein.com/projects/xsltjson/
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
   */
  class RayfishSerializer extends JsonSerializer {

    /**
     * @param \DOMElement $node
     * @return \stdClass
     */
    protected function getNode(\DOMElement $node): \stdClass {
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
          $childNode instanceof \DOMText &&
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

    private function getAttributes(\DOMElement $node): array {
      $result = [];
      foreach ($node->attributes as $attributeNode) {
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
