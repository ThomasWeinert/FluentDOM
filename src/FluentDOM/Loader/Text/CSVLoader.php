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

namespace FluentDOM\Loader\Text {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\DOM\Element;
  use FluentDOM\DOM\Implementation;
  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Json\JsonDOMLoader;
  use FluentDOM\Loader\LoaderOptions;
  use FluentDOM\Loader\LoaderResult;
  use FluentDOM\Loader\LoaderSupports;
  use FluentDOM\Utility\QualifiedName;

  /**
   * Load a CSVLoader file
   */
  class CSVLoader implements Loadable {

    use LoaderSupports;

    public const CONTENT_TYPES = ['text/csv'];

    private const XMLNS = JsonDOMLoader::XMLNS;
    private const DEFAULT_QNAME = '_';

    private string $_delimiter = ',';
    private string $_enclosure = '"';
    private string $_escape = '\\';

    /**
     * @throws \InvalidArgumentException
     * @throws InvalidSource|\DOMException|UnattachedNode
     * @see Loadable::load
     */
    public function load(mixed $source, string $contentType, iterable $options = []): ?LoaderResult {
      $options = $this->getOptions($options);
      $hasHeaderLine = isset($options['HEADER']) ? (bool)$options['HEADER'] : !isset($options['FIELDS']);
      $this->configure($options);
      if ($this->supports($contentType) && ($lines = $this->getLines($source, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $document->appendChild($list = $document->createElementNS(self::XMLNS, 'json:json'));
        $list->setAttributeNS(self::XMLNS, 'json:type', 'array');
        $this->appendLines($list, $lines, $hasHeaderLine, $options['FIELDS'] ?? NULL);
        return new LoaderResult($document, $contentType);
      }
      return NULL;
    }

    /**
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|LoaderOptions $options
     * @return DocumentFragment|NULL
     * @throws \InvalidArgumentException
     * @throws InvalidSource
     * @throws \DOMException|UnattachedNode
     * @see Loadable::loadFragment
     *
     */
    public function loadFragment($source, string $contentType, $options = []): ?DocumentFragment {
      $options = $this->getOptions($options);
      $options[LoaderOptions::ALLOW_FILE] = FALSE;
      $hasHeaderLine = (
        (!isset($options['FIELDS'])) && isset($options['HEADER']) && $options['HEADER']
      );
      $this->configure($options);
      if ($this->supports($contentType) && ($lines = $this->getLines($source, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $fragment = $document->createDocumentFragment();
        $this->appendLines($fragment, $lines, $hasHeaderLine, $options['FIELDS'] ?? NULL);
        return $fragment;
      }
      return NULL;
    }

    /**
     * Append the provided lines to the parent.
     * @throws \DOMException|UnattachedNode
     */
    private function appendLines(
      \DOMNode $parent, iterable $lines, bool $hasHeaderLine, array $columns = NULL
    ): void {
      $document = Implementation::getNodeDocument($parent);
      $headers = NULL;
      /** @var array $record */
      foreach ($lines as $record) {
        if ($headers === NULL) {
          $headers = $this->getHeaders(
            $record, $hasHeaderLine, $columns
          );
          if ($hasHeaderLine) {
            continue;
          }
        }
        /** @var Element $node */
        $node = $parent->appendChild($document->createElement(self::DEFAULT_QNAME));
        foreach ($record as $index => $field) {
          if (isset($headers[$index])) {
            $this->appendField($node, (string)$headers[$index], (string)$field);
          }
        }
      }
    }

    /**
     * @throws \DOMException
     */
    private function appendField(Element $parent, string $name, string $value): void {
      $qname = QualifiedName::normalizeString($name, self::DEFAULT_QNAME);
      $child = $parent->appendElement($qname, $value);
      if ($qname !== $name) {
        $child->setAttributeNS(self::XMLNS, 'json:name', $name);
      }
    }

    private function getHeaders(array $record, bool $hasHeaderLine, array $columns = NULL): array {
      if (\is_array($columns)) {
        $headers = [];
        foreach ($record as $index => $field) {
          $key = $hasHeaderLine ? $field : $index;
          $headers[$index] = $columns[$key] ?? FALSE;
        }
        return $headers;
      }
      if ($hasHeaderLine) {
        return $record;
      }
      return \array_keys($record);
    }

    /**
     * @throws InvalidSource
     */
    private function getLines(mixed $source, LoaderOptions $options): ?\Traversable {
      $result = NULL;
      if (\is_string($source)) {
        $options->isAllowed($sourceType = $options->getSourceType($source));
        if ($sourceType === LoaderOptions::IS_FILE) {
          $result = new \SplFileObject($source);
        } else {
          $result = new \SplFileObject('data://text/csv;base64,'.\base64_encode($source));
        }
        $result->setFlags(\SplFileObject::READ_CSV);
        $result->setCsvControl(
          $this->_delimiter,
          $this->_enclosure,
          $this->_escape
        );
      } elseif (\is_array($source)) {
        $result = new \ArrayIterator($source);
      } elseif ($source instanceof \Traversable) {
        $result = $source;
      }
      return empty($result) ? NULL : $result;
    }

    private function configure(iterable $options): void {
      $this->_delimiter = $options['DELIMITER'] ?? $this->_delimiter;
      $this->_enclosure = $options['ENCLOSURE'] ?? $this->_enclosure;
      $this->_escape = $options['ESCAPE'] ?? $this->_escape;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getOptions(iterable $options): LoaderOptions {
      return new LoaderOptions(
        $options,
        [
          LoaderOptions::CB_IDENTIFY_STRING_SOURCE => function($source) {
            return (is_string($source) && (str_contains($source, "\n")));
          }
        ]
      );
    }
  }

}
