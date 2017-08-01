<?php
/**
 * Allow an object to be appendable to a FluentDOM\DOM\Element
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM {

  /**
   * @property bool $formatOutput
   * @property bool $optimizeNamespaces
   */
  class Creator {

    /**
     * @var DOM\Document
     */
    private $_document;

    /**
     * @var bool
     */
    private $_optimizeNamespaces = TRUE;

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct(string $version = '1.0', string $encoding = 'UTF-8') {
      $this->_document = new DOM\Document($version, $encoding);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool {
      switch ($name) {
      case 'formatOutput' :
        return isset($this->_document->{$name});
      case 'optimizeNamespaces' :
        return TRUE;
      }
      return FALSE;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
      switch ($name) {
      case 'formatOutput' :
        return $this->_document->{$name};
      case 'optimizeNamespaces' :
        return $this->_optimizeNamespaces;
      }
      return NULL;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) {
      switch ($name) {
      case 'formatOutput' :
        $this->_document->{$name} = $value;
        return;
      case 'optimizeNamespaces' :
        $this->_optimizeNamespaces = (bool)$value;
        return;
      }
      $this->{$name} = $value;
    }

    /**
     * If the creator is cloned, a clone of the dom document is needed, too.
     *
     */
    public function __clone() {
      $this->_document = clone $this->_document;
    }

    /**
     * @param string $prefix
     * @param string $namespaceURI
     * @throws \LogicException
     */
    public function registerNamespace(string $prefix, string $namespaceURI) {
      $this->_document->registerNamespace($prefix, $namespaceURI);
    }

    /**
     * @param string $name
     * @param mixed ...$parameters
     * @return Creator\Node
     * @throws \LogicException
     */
    public function __invoke(string $name, ...$parameters) {
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
     * @param string $name
     * @param mixed ...$parameters
     * @return DOM\Element
     * @throws \LogicException
     */
    public function element(string $name, ...$parameters): DOM\Element {
      $node = $this->_document->createElement($name);
      foreach ($parameters as $parameter) {
        $node->append($parameter);
      }
      return $node;
    }

    /**
     * @param string $content
     * @return DOM\CdataSection
     */
    public function cdata(string $content): DOM\CdataSection {
      return $this->_document->createCdataSection($content);
    }

    /**
     * @param string $content
     * @return DOM\Comment
     */
    public function comment($content): DOM\Comment {
      return $this->_document->createComment($content);
    }

    /**
     * @param string $target
     * @param string $content
     * @return DOM\ProcessingInstruction
     */
    public function pi($target, $content): DOM\ProcessingInstruction {
      return $this->_document->createProcessingInstruction($target, $content);
    }

    /**
     * @param array|\Traversable $traversable
     * @param callable $map
     * @return Appendable
     */
    public function each($traversable, callable $map = NULL): Appendable {
      return new Creator\Nodes($traversable, $map);
    }
  }
}