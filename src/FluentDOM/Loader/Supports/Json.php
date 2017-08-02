<?php

namespace FluentDOM\Loader\Supports {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\DOM\Element;
  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Result;
  use FluentDOM\Loader\Supports;

  trait Json {

    use Supports;

    /**
     * Load the json string into an DOMDocument
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     * @throws \Exception
     * @throws \FluentDOM\Exceptions\InvalidSource
     */
    public function load($source, string $contentType, $options = []) {
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
     * @throws \Exception
     * @throws \FluentDOM\Exceptions\InvalidSource
     */
    public function loadFragment($source, string $contentType, $options = []) {
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
     * @throws \Exception
     * @throws \FluentDOM\Exceptions\InvalidSource
     */
    private function getJson($source, string $contentType, $options)  {
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
     * @throws \InvalidArgumentException
     */
    public function getOptions($options): Options {
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
     * @param \DOMNode|Element $node
     * @param mixed $json
     */
    abstract protected function transferTo(\DOMNode $node, $json);

    /**
     * @param mixed $value
     * @return string
     */
    private function getValueAsString($value): string {
      if (is_bool($value)) {
        return $value ? 'true' : 'false';
      }
      return (string)$value;
    }

    /**
     * @param string $nodeName
     * @param \stdClass $properties
     * @param \DOMNode $parent
     * @return string|NULL
     */
    private function getNamespaceForNode(
      string $nodeName, \stdClass $properties, \DOMNode $parent
    ) {
      $prefix = substr($nodeName, 0, strpos($nodeName, ':'));
      $xmlns = $this->getNamespacePropertyName($prefix);
      return $properties->{$xmlns} ?? $parent->lookupNamespaceUri(empty($prefix) ? NULL : $prefix);
    }

    /**
     * Get the property name for a namespace prefix
     *
     * @param string $prefix
     * @return string
     */
    protected function getNamespacePropertyName($prefix) {
      return empty($prefix) ? 'xmlns' : 'xmlns:'.$prefix;
    }
  }
}