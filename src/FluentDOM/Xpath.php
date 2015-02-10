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
   *
   * @property boolean registerNodeNamespaces
   */
  class Xpath extends \DOMXPath {

    /**
     * @var bool
     */
    private $_registerNodeNamespaces = FALSE;

    /**
     * @var \DOMDocument|null
     */
    private $_documentReference = NULL;

    /**
     * HHVM and some old PHP versions do not have a $document property by default
     * Add it is added if it was not found after executing parent constructor.
     *
     * @param \DOMDocument $dom
     */
    public function __construct(\DOMDocument $dom) {
      parent::__construct($dom);
      // store the document reference to avoid optimization to DOMDocument
      $this->_documentReference = $dom;
      // @codeCoverageIgnoreStart
      if (!isset($this->document)) {
        $this->document = $dom;
      }
      // @codeCoverageIgnoreEnd
    }

    /**
     * If the owner document is a FluentDOM\Document register the namespace on the
     * document object, too.
     *
     * @param string $prefix
     * @param string $namespace
     * @return bool
     */
    public function registerNamespace($prefix, $namespace) {
      if ($this->_documentReference instanceOf Document &&
          $this->_documentReference->getNamespace($prefix) !== $namespace) {
        $this->_documentReference->registerNameSpace($prefix, $namespace);
      }
      return parent::registerNamespace($prefix, $namespace);
    }

    /**
     * Fetch nodes or scalar values from the DOM using Xpath expression.
     *
     * The main difference to DOMXpath::evaluate() is the handling of the third
     * argument. Namespace registration can be changed using the property and
     * is disabled by default.
     *
     * @param string $expression
     * @param \DOMNode $contextNode
     * @param NULL|boolean $registerNodeNS
     * @return string|float|bool|\DOMNodeList
     */
    public function evaluate($expression, \DOMNode $contextNode = NULL, $registerNodeNS = NULL) {
      $registerNodeNS = (NULL === $registerNodeNS)
        ? $this->_registerNodeNamespaces : $registerNodeNS;
      return parent::evaluate($expression, $contextNode, (bool)$registerNodeNS);
    }

    /**
     * Fetch nodes or scalar values from the DOM using Xpath expression.
     *
     * @param string $expression
     * @param \DOMNode $contextNode
     * @return string|float|bool|\DOMNodeList
     */
    public function __invoke($expression, $contextNode = NULL) {
      return $this->evaluate($expression, $contextNode);
    }

    /**
     * Fetch nodes defined by the xpath expression and return the node list.
     *
     * This method is deprecated and only implemented for BC. So this method
     * calls evaluate().
     *
     * @deprecated
     * @param string $expression
     * @param \DOMNode $contextNode
     * @param NULL|boolean $registerNodeNS
     * @return \DOMNodeList
     */
    public function query($expression, \DOMNode $contextNode = NULL, $registerNodeNS = NULL) {
      trigger_error(
        'Please use XPath::evaluate() not XPath::query().', E_USER_DEPRECATED
      );
      $result = $this->evaluate($expression, $contextNode, $registerNodeNS);
      return $result instanceof \DOMNodeList ? $result : NULL;
    }

    /**
     * Returns the first node matched by the expression or NULL.
     *
     * @param string $expression
     * @param \DOMNode $contextNode
     * @param NULL|boolean $registerNodeNS
     * @return \DOMNode|null
     */
    public function firstOf($expression, \DOMNode $contextNode = NULL, $registerNodeNS = NULL) {
      $nodes = $this->evaluate($expression, $contextNode, $registerNodeNS);
      if ($nodes instanceof \DOMNodeList && $nodes->length > 0) {
        return $nodes->item(0);
      }
      return NULL;
    }

    /**
     * Quote (and escape) a value to use it in an xpath expression.
     *
     * Xpath 1 does not have a way to escape quotes, it only allows
     * double quotes in single quoted literals and single quotes in double
     * quoted literals.
     *
     * If both quotes are included in the string, the method will generate a
     * concat() function call.
     *
     * @param string $string
     * @return string
     */
    public function quote($string) {
      $string = str_replace("\x00", '', $string);
      $hasSingleQuote = FALSE !== strpos($string, "'");
      if ($hasSingleQuote) {
        $hasDoubleQuote = FALSE !== strpos($string, '"');
        if ($hasDoubleQuote) {
          $result = '';
          preg_match_all('("[^\']*|[^"]+)', $string, $matches);
          foreach ($matches[0] as $part) {
            $quoteChar = (substr($part, 0, 1) === '"') ? "'" : '"';
            $result .= ", ".$quoteChar.$part.$quoteChar;
          }
          return 'concat('.substr($result, 2).')';
        } else {
          return '"'.$string.'"';
        }
      } else {
        return "'".$string."'";
      }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
      switch ($name) {
      case 'registerNodeNamespaces' :
        return TRUE;
      }
      return isset($this->$name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
      switch ($name) {
      case 'registerNodeNamespaces' :
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
      case 'registerNodeNamespaces' :
        $this->_registerNodeNamespaces = (bool)$value;
        return;
      }
      $this->$name = $value;
    }

    /**
     * @param string $name
     */
    public function __unset($name) {
      switch ($name) {
      case 'registerNodeNamespaces' :
        $this->_registerNodeNamespaces = FALSE;
      }
      unset($this->$name);
    }
  }
}
