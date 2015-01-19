<?php
/**
 * Load a iCalendar (*.ics) file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Text {

  use FluentDOM\Document;
  use FluentDOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;
  use FluentDOM\QualifiedName;

  /**
   * Load a iCalendar (*.ics) file
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
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      $hasHeaderLine = isset($options['HEADER']) ? (bool)$options['HEADER'] : !isset($options['FIELDS']);
      $this->configure($options);
      if ($this->supports($contentType) && ($lines = $this->getLines($source))) {
        $dom = new Document('1.0', 'UTF-8');
        $dom->appendChild($list = $dom->createElementNS(self::XMLNS, 'json:json'));
        $list->setAttributeNS(self::XMLNS, 'json:type', 'array');
        $headers = NULL;
        foreach ($lines as $line) {
          if ($headers === NULL) {
            $headers = $this->getHeaders(
              $line, $hasHeaderLine, isset($options['FIELDS']) ? $options['FIELDS'] : NULL
            );
            if ($hasHeaderLine) {
              continue;
            }
          }
          $list->appendChild($node =  $dom->createElement(self::DEFAULT_QNAME));
          foreach ($line as $index => $field) {
            if (isset($headers[$index])) {
              $this->appendField($node, $headers[$index], $field);
            }
          }
        }
        return $dom;
      }
      return NULL;
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

    private function getLines($source) {
      $result = null;
      if (is_string($source)) {
        if ($this->isFile($source)) {
          $result = new \SplFileObject($source);
        } elseif (is_string($source)) {
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

    private function isFile($source) {
      return (is_string($source) && (FALSE === strpos($source, "\n")));
    }

    /**
     * @param array $options
     */
    private function configure(array $options) {
      $this->_delimiter = isset($options['DELIMITER']) ? $options['DELIMITER'] : $this->_delimiter;
      $this->_enclosure = isset($options['ENCLOSURE']) ? $options['ENCLOSURE'] : $this->_enclosure;
      $this->_escape = isset($options['ESCAPE']) ? $options['ESCAPE'] : $this->_escape;
    }
  }
}