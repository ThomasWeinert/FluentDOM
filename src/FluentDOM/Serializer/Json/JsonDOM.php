<?php
/**
 * Serialize a DOM to Json using the JsonDOM rules
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Serializer\Json {

  use FluentDOM\Serializer\Json;
  use FluentDOM\Xpath;

  /**
   * Serialize a DOM to Json using the JsonDOM rules.
   *
   * If you use it on a normal XML you will loose a lot of information.
   * It will work best for imported JSON.
   *
   * @license http://www.opensource.org/licenses/mit-license.php The MIT License
   * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
   */
  class JsonDOM extends Json {

    const XMLNS = 'urn:carica-json-dom.2013';

    /**
     * @param \DOMElement $node
     * @return \stdClass
     */
    protected function getNode(\DOMElement $node) {
      $xpath = new Xpath($node->ownerDocument);
      if ($node->hasAttributeNS(self::XMLNS, 'type')) {
        $type = $node->getAttributeNS(self::XMLNS, 'type');
      } else {
        $type = $xpath('count(*) > 0', $node) ? 'object' : 'string';
      }
      switch ($type) {
      case 'object' :
        $result = new \stdClass();
        foreach ($xpath('*', $node) as $child) {
          if ($child->hasAttributeNS(self::XMLNS, 'name')) {
            $name = $child->getAttributeNS(self::XMLNS, 'name');
          } else {
            $name = $child->localName;
          }
          $result->{$name} = $this->getNode($child);
        }
        break;
      case 'array' :
        $result = [];
        foreach ($xpath('*', $node) as $child) {
          $result[] = $this->getNode($child);
        }
        break;
      case 'number' :
        return (float)$node->nodeValue;
      case 'boolean' :
        return $node->nodeValue == 'true' ? true : false;
      case 'null' :
        return null;
      default :
        return $node->nodeValue;
      }
      return $result;
    }
  }
}