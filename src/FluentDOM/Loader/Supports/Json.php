<?php

namespace FluentDOM\Loader\Supports {

  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Supports;
  use FluentDOM\Loader\Result;

  trait Json {

    use Supports;

    /**
     * Load the json string into an DOMDocument
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, $options = []) {
      if (FALSE !== ($json = $this->getJson($source, $contentType, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $this->transferTo($document, $json);
        return $document;
      }
      return NULL;
    }

    /**
     * @see Loadable::loadFragment
     *
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment($source, $contentType, $options = []) {
      if (FALSE !== ($json = $this->getJson($source, $contentType, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $fragment = $document->createDocumentFragment();
        $this->transferTo($fragment, $json);
        return $fragment;
      }
      return NULL;
    }

    /**
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return mixed
     */
    private function getJson($source, $contentType, $options)  {
      if ($this->supports($contentType)) {
        if (is_string($source)) {
          $json = FALSE;
          $settings = $this->getOptions($options);
          if ($settings->isAllowed($sourceType = $settings->getSourceType($source))) {
            switch ($sourceType) {
              /** @noinspection PhpMissingBreakStatementInspection */
            case Options::IS_FILE :
              $source = file_get_contents($source);
            case Options::IS_STRING :
              $json = json_decode($source);
              if (!($json || is_array($json))) {
                throw new LoadingError\Json(
                  is_callable('json_last_error') ? json_last_error() : -1
                );
              }
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
     * @param array|\Traversable|Options $options
     * @return Options
     */
    public function getOptions($options) {
      $result = new Options(
        $options,
        [
          Options::CB_IDENTIFY_STRING_SOURCE => function($source) {
            return $this->startsWith($source, '{') || $this->startsWith($source, '[');
          }
        ]
      );
      return $result;
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