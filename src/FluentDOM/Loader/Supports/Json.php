<?php

namespace FluentDOM\Loader\Supports {

  use FluentDOM\Document;
  use FluentDOM\Exceptions\JsonError;
  use FluentDOM\Loader\Supports;

  trait Json {

    use Supports;

    /**
     * Load the json string into an DOMDocument
     *
     * @param mixed $source
     * @param string $contentType
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if (FALSE !== ($json = $this->getJson($source, $contentType))) {
        $dom = new Document('1.0', 'UTF-8');
        $this->transferTo($dom, $json);
        return $dom;
      }
      return NULL;
    }

    /**
     * @param mixed $source
     * @param string $contentType
     * @return mixed
     */
    private function getJson($source, $contentType)  {
      if ($this->supports($contentType)) {
        if (is_string($source)) {
          $json = FALSE;
          if (!$this->startsWith($source, '{[')) {
            $source = file_get_contents($source);
          }
          if ($this->startsWith($source, '{[')) {
            $json = json_decode($source);
            if (!($json || is_array($json))) {
              throw new JsonError(
                is_callable('json_last_error') ? json_last_error() : -1
              );
            }
          }
        } else {
          $json = $source;
        }
        return ($json || is_array($json)) ? $json : FALSE;
      }
      return FALSE;
    }

    /**
     * @param \DOMNode|\DOMElement $node
     * @param mixed $json
     */
    protected abstract function transferTo(\DOMNode $node, $json);

    /**
     * @param mixed $value
     * @return string
     */
    private function getValueAsString($value) {
      if (is_bool($value)) {
        return $value ? 'true' : 'false';
      } else {
        return (string)$value;
      }
    }

    /**
     * @param string $nodeName
     * @param \stdClass $properties
     * @param \DOMNode $parent
     * @return string
     */
    private function getNamespaceForNode(
      $nodeName, \stdClass $properties, \DOMNode $parent
    ) {
      $prefix = substr($nodeName, 0, strpos($nodeName, ':'));
      $xmlns = $this->getNamespacePropertyName($prefix);
      return isset($properties->{$xmlns})
        ? $properties->{$xmlns}
        : $parent->lookupNamespaceUri(empty($prefix) ? NULL : $prefix);
    }

    /**
     * Get the property name for a namespace prefix
     *
     * @param string $prefix
     * @return string
     */
    private function getNamespacePropertyName($prefix) {
      return empty($prefix) ? 'xmlns' : 'xmlns:'.$prefix;
    }
  }
}