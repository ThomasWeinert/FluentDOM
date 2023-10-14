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

  class NamespaceStack {

    private array $_stack = [];

    private NamespaceDefinition $_current;

    public function __construct() {
      $this->clear();
    }

    public function clear(): void {
      $this->_stack = [];
      $this->_current = new NamespaceDefinition();
    }

    public function push(): void {
      $this->_current->increaseDepth();
    }

    public function pop(): void {
      if ($this->_current->getDepth() < 1) {
        $this->_current = end($this->_stack);
      } else {
        $this->_current->decreaseDepth();
      }
    }

    public function isDefined(?string $prefix, ?string $namespaceURI): bool {
      return ($this->_current->resolveNamespace((string)$prefix) === $namespaceURI);
    }

    public function add(?string $prefix, ?string $namespaceURI): void {
      if ($this->_current->getDepth() > 0) {
        $this->_stack[] = $this->_current;
        $this->_current = new NamespaceDefinition($this->_current);
      }
      $this->_current->registerNamespace($prefix, $namespaceURI);
    }
  }
}
