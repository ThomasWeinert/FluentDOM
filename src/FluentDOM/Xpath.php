<?php
/**
 * FluentDOM\Xpath extends PHPs DOMXpath class. It disables the
 * automatic namespace registration by default and, throws notices for the query method.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\Xpath extends PHPs DOMXpath class. It disables the
   * automatic namespace registration by default and, throws notices for the query method.
   */
  class Xpath extends \DOMXPath {

    private $_registerNodeNamespaces = FALSE;

    /**
     * If the owner document is a FluentDOM\Document register the namesspace on the
     * document object, too.
     *
     * @param string $prefix
     * @param string $namespace
     * @return bool
     */
    public function registerNamespace($prefix, $namespace) {
      if ($this->document instanceOf Document &&
          $this->document->getNamespace($prefix) !== $namespace) {
        $this->document->registerNameSpace($prefix, $namespace);
      }
      return parent::registerNamespace($prefix, $namespace);
    }

    /**
     * @param string $expression
     * @param \DOMNode $contextNode
     * @param NULL|boolean $registerNodeNS
     * @return mixed
     */
    public function evaluate($expression, \DOMNode $contextNode = NULL, $registerNodeNS = NULL) {
      $registerNodeNS = $registerNodeNS ?: $this->_registerNodeNamespaces;
      return parent::evaluate($expression, $contextNode, (bool)$registerNodeNS);
    }

    /**
     * @param string $expression
     * @param \DOMNode $contextNode
     * @param NULL|boolean $registerNodeNS
     * @return \DOMNodeList
     */
    public function query($expression, \DOMNode $contextNode = NULL, $registerNodeNS = NULL) {
      trigger_error(
        'Please use XPath::evaluate() not XPath::query().', E_USER_DEPRECATED
      );
      $registerNodeNS = $registerNodeNS ?: $this->_registerNodeNamespaces;
      return parent::query($expression, $contextNode, (bool)$registerNodeNS);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name) {
      switch ($name) {
      case 'registerNodeNamesspaces' :
        return TRUE;
      }
      return isset($this->$name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name) {
      switch ($name) {
      case 'registerNodeNamesspaces' :
        return $this->_registerNodeNamespaces;
      }
      return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value) {
      switch ($name) {
      case 'registerNodeNamesspaces' :
        $this->_registerNodeNamespaces = (bool)$value;
      }
      return $this->$name = $value;
    }

    /**
     * @param string $name
     */
    public function __unset($name) {
      switch ($name) {
      case 'registerNodeNamesspaces' :
        $this->_registerNodeNamespaces = FALSE;
      }
      unset($this->$name);
    }
  }
}