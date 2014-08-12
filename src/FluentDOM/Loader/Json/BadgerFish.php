<?php
/**
 * Load a DOM document from a json string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Json {

  use FluentDOM\Document;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;

  /**
   * Load a DOM document from a json string or file
   */
  class BadgerFish implements Loadable {

    use Supports\Json;

    /**
     * @return string[]
     */
    public function getSupported() {
      return ['badgerfish', 'application/badgerfish', 'application/badgerfish+json'];
    }


    /**
     * Load the json string into an DOMDocument
     *
     * @param mixed $source
     * @param string $contentType
     * @throws \UnexpectedValueException
     * @return Document|NULL
     */
    public function load($source, $contentType) {
      $json = $source;
      if (is_string($source)) {
        $json = $this->getJson($source);
      }
      if ($json || is_object($json)) {
        $dom = new Document('1.0', 'UTF-8');
        $this->transferTo($dom, $json);
        return $dom;
      }
      return NULL;
    }

    /**
     * @param \DOMNode|\DOMElement $node
     * @param \stdClass $json
     */
    public function transferTo(\DOMNode $node, \stdClass $json) {
      /** @var Document $dom */
      $dom = $node->ownerDocument ?: $node;
      if (is_object($json)) {
        foreach ($json as $name => $data) {
          if ($name === '@xmlns') {
            //namespaces
            foreach ($data as $key => $namespace) {
              $prefix = $key == '$' ? NULL : $key;
              if ($node->lookupNamespaceUri($prefix) != $namespace) {
                $node->setAttribute(
                  empty($prefix) ? 'xmlns' : 'xmlns:' . $prefix,
                  $namespace
                );
              }
            }
          } elseif ($name === '$') {
            // text content
            $node->appendChild(
              $dom->createTextNode((string)$data)
            );
          } elseif (substr($name, 0, 1) === '@') {
            // attributes
            $name = substr($name, 1);
            $namespace = $this->getNamespace($name, new \stdClass(), $node);
            $attribute = empty($namespace)
              ? $dom->createAttribute($name)
              : $dom->createAttributeNS($namespace, $name);
            $attribute->value = (string)$data;
            $node->setAttributeNode($attribute);
          } else {
            // child node
            $namespace = $this->getNamespace(
              $name,
              isset($data->{'@xmlns'}) ? $data->{'@xmlns'} : new \stdClass(),
              $dom
            );
            if (!is_array($data)) {
              $data = [$data];
            }
            foreach ($data as $dataChild) {
              $node->appendChild(
                $child = empty($namespace)
                  ? $dom->createElement($name)
                  : $dom->createElementNS($namespace, $name)
              );
              $this->transferTo($child, $dataChild);
            }
          }
        }
      }
    }

    /**
     * @param string $nodeName
     * @param \stdClass $namespaces
     * @param \DOMNode $node
     * @return string
     */
    private function getNamespace(
      $nodeName, \stdClass $namespaces, \DOMNode $node
    ) {
      if (strpos($nodeName, ':') >= 0) {
        $prefix = substr($nodeName, 0, strpos($nodeName, ':'));
      } else {
        $prefix = '';
      }
      $xmlns = empty($prefix) ? '$' : $prefix;
      return isset($namespaces->{$xmlns})
        ? $namespaces->{$xmlns}
        : $node->lookupNamespaceUri($prefix || NULL);
    }
  }
}