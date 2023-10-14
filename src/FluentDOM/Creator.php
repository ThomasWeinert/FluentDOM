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

namespace FluentDOM {

  use FluentDOM\Exceptions\UndeclaredPropertyError;

  /**
   * @property bool $formatOutput
   * @property bool $optimizeNamespaces
   * @property-read DOM\Document $document
   */
  class Creator {

    private DOM\Document $_document;

    private bool $_optimizeNamespaces = TRUE;

    public function __construct(string $version = '1.0', string $encoding = 'UTF-8') {
      $this->_document = new DOM\Document($version, $encoding);
    }

    public function __isset(string $name): bool {
      return match ($name) {
        'document' => true,
        'formatOutput' => isset($this->_document->{$name}),
        'optimizeNamespaces' => TRUE,
        default => FALSE,
      };
    }

    public function __get(string $name): mixed {
      return match ($name) {
        'document' => $this->_document,
        'formatOutput' => $this->_document->{$name},
        'optimizeNamespaces' => $this->_optimizeNamespaces,
        default => NULL,
      };
    }

    public function __set(string $name, mixed $value): void {
      match  ($name) {
        'formatOutput' => $this->_document->{$name} = $value,
        'optimizeNamespaces' => $this->_optimizeNamespaces = (bool)$value,
        default => throw new UndeclaredPropertyError($this, $name)
      };
    }

    /**
     * If the creator is cloned, a clone of the dom document is needed, too.
     */
    public function __clone(): void {
      $this->_document = clone $this->_document;
    }

    public function registerNamespace(string $prefix, string $namespaceURI): void {
      $this->_document->registerNamespace($prefix, $namespaceURI);
    }

    /**
     * @throws \LogicException|Exceptions\UnattachedNode|\DOMException
     */
    public function __invoke(string $name, mixed ...$parameters): Creator\Node {
      return new Creator\Node(
        $this,
        $this->_document,
        $this->element($name, ...$parameters)
      );
    }

    /**
     * Create an Element node and configure it.
     *
     * The first argument is the node name. All other arguments are flexible.
     *
     * - Arrays are set as attributes
     * - Attribute and Namespace nodes are set as attributes
     * - Nodes are appended as child nodes
     * - FluentDOM\Appendable instances are appended
     * - Strings or objects castable to string are appended as text nodes
     *
     * @throws \LogicException
     * @throws Exceptions\UnattachedNode|\DOMException
     */
    public function element(string $name, mixed ...$parameters): DOM\Element {
      $node = $this->_document->createElement($name);
      $node->append(...$parameters);
      return $node;
    }

    public function cdata(string $content): DOM\CdataSection {
      return $this->_document->createCdataSection($content);
    }

    public function comment(string $content): DOM\Comment {
      return $this->_document->createComment($content);
    }

    public function pi(string $target, string $content): DOM\ProcessingInstruction {
      return $this->_document->createProcessingInstruction($target, $content);
    }

    public function each(iterable $traversable, callable $map = NULL): Appendable {
      return new Creator\Nodes($traversable, $map);
    }
  }
}
