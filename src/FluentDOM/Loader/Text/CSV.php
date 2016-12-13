<?php
/**
 * Load a CSV file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2016 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Text {

  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Supports;
  use FluentDOM\QualifiedName;
  use FluentDOM\Loader\Result;

  /**
   * Load a CSV file
   */
  class CSV implements Loadable {

    use Supports;

    const XMLNS = 'urn:carica-json-dom.2013';
    const DEFAULT_QNAME = '_';

    private $_delimiter = ',';
    private $_enclosure = '"';
    private $_escape = '\\';

    /**
     * @return string[]
     */
    public function getSupported() {
      return ['text/csv'];
    }

    /**
     * @see Loadable::load
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, $options = []) {
      $options = $this->getOptions($options);
      $hasHeaderLine = isset($options['HEADER']) ? (bool)$options['HEADER'] : !isset($options['FIELDS']);
      $this->configure($options);
      if ($this->supports($contentType) && ($lines = $this->getLines($source, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $document->appendChild($list = $document->createElementNS(self::XMLNS, 'json:json'));
        $list->setAttributeNS(self::XMLNS, 'json:type', 'array');
        $this->appendLines($list, $lines, $hasHeaderLine, isset($options['FIELDS']) ? $options['FIELDS'] : NULL);
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
      $options = $this->getOptions($options);
      $options[Options::ALLOW_FILE] = FALSE;
      $hasHeaderLine = isset($options['FIELDS']) ? FALSE : (isset($options['HEADER']) && $options['HEADER']);
      $this->configure($options);
      if ($this->supports($contentType) && ($lines = $this->getLines($source, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $fragment = $document->createDocumentFragment();
        $this->appendLines($fragment, $lines, $hasHeaderLine, isset($options['FIELDS']) ? $options['FIELDS'] : NULL);
        return $fragment;
      }
      return NULL;
    }

    /**
     * Append the provided lines to the parent.
     *
     * @param \DOMNode $parent
     * @param $lines
     * @param $hasHeaderLine
     * @param array $fields
     */
    private function appendLines(\DOMNode $parent, $lines, $hasHeaderLine, array $fields = NULL) {
      $document = $parent instanceof \DOMDocument ? $parent : $parent->ownerDocument;
      $headers = NULL;
      foreach ($lines as $line) {
        if ($headers === NULL) {
          $headers = $this->getHeaders(
            $line, $hasHeaderLine, $fields
          );
          if ($hasHeaderLine) {
            continue;
          }
        }
        /** @var Element $node */
        $parent->appendChild($node = $document->createElement(self::DEFAULT_QNAME));
        foreach ($line as $index => $field) {
          if (isset($headers[$index])) {
            $this->appendField($node, $headers[$index], $field);
          }
        }
      }
    }

    /**
     * @param Element $parent
     * @param string $name
     * @param string $value
     */
    private function appendField(Element $parent, $name, $value) {
      $qname = QualifiedName::normalizeString($name, self::DEFAULT_QNAME);
      $child = $parent->appendElement($qname, $value);
      if ($qname !== $name) {
        $child->setAttributeNS(self::XMLNS, 'json:name', $name);
      }
    }

    /**
     * @param array $line
     * @param bool $hasHeaderLine
     * @param array|NULL $fields
     * @return array
     */
    private function getHeaders(array $line, $hasHeaderLine, $fields = NULL) {
      if (is_array($fields)) {
        $headers = [];
        foreach ($line as $index => $field) {
          $key = $hasHeaderLine ? $field : $index;
          $headers[$index] = isset($fields[$key]) ? $fields[$key] : FALSE;
        }
        return $headers;
      } elseif ($hasHeaderLine) {
        return $line;
      } else {
        return array_keys($line);
      }
    }

    private function getLines($source, Options $options) {
      $result = null;
      if (is_string($source)) {
        $options->isAllowed($sourceType = $options->getSourceType($source));
        switch ($sourceType) {
        case Options::IS_FILE :
          $result = new \SplFileObject($source);
          break;
        default :
          $result = new \SplFileObject('data://text/csv;base64,'.base64_encode($source));
        }
        $result->setFlags(\SplFileObject::READ_CSV);
        $result->setCsvControl(
          $this->_delimiter,
          $this->_enclosure,
          $this->_escape
        );
      } elseif (is_array($source)) {
        $result = new \ArrayIterator($source);
      } elseif ($source instanceof \Traversable) {
        $result = $source;
      }
      return (empty($result)) ? NULL : $result;
    }

    /**
     * @param array|\Traversable|Options $options
     */
    private function configure($options) {
      $this->_delimiter = isset($options['DELIMITER']) ? $options['DELIMITER'] : $this->_delimiter;
      $this->_enclosure = isset($options['ENCLOSURE']) ? $options['ENCLOSURE'] : $this->_enclosure;
      $this->_escape = isset($options['ESCAPE']) ? $options['ESCAPE'] : $this->_escape;
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
            return (is_string($source) && (FALSE !== strpos($source, "\n")));
          }
        ]
      );
      return $result;
    }
  }

}