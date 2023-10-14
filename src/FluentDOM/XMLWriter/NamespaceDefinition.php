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

namespace FluentDOM\XMLWriter {

  use FluentDOM\Utility\NamespaceResolver;
  use FluentDOM\Utility\Namespaces;

  class NamespaceDefinition implements \IteratorAggregate, NamespaceResolver {


    private int $_indent;
    private Namespaces $_namespaces;

    public function __construct(iterable $inherit = NULL) {
      $this->_indent = 0;
      $this->_namespaces = new Namespaces($inherit);
    }

    public function getDepth(): int {
      return $this->_indent;
    }

    public function increaseDepth(): int {
      return ++$this->_indent;
    }

    public function decreaseDepth(): int {
      if ($this->_indent > 0) {
        return --$this->_indent;
      }
      throw new \LogicException('Did not resolve namespace levels properly.');
    }

    public function registerNamespace($prefix, $namespaceURI): void {
      $this->_namespaces[$prefix] = $namespaceURI;
    }

    public function resolveNamespace($prefix): ?string {
      try {
        return $this->_namespaces->resolveNamespace($prefix);
      } catch (\LogicException) {
        return '';
      }
    }

    public function getIterator(): Namespaces {
      return $this->_namespaces;
    }
  }
}
