<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\DOM {

  /**
   * FluentDOM\DOM\Xpath extends PHPs DOMXpath class. It disables the
   * automatic namespace registration by default and, throws notices for the query method.
   *
   * @property bool $registerNodeNamespaces
   */
  class Xpath extends \DOMXPath {

    /**
     * @var bool
     */
    private $_registerNodeNamespaces = FALSE;

    /**
     * @var \DOMDocument
     */
    private $_documentReference;

    /**
     * @param \DOMDocument $document
     */
    public function __construct(\DOMDocument $document) {
      parent::__construct($document);
      // store the document reference to avoid optimization to DOMDocument
      $this->_documentReference = $document;
      $this->registerNodeNamespaces = FALSE;
    }

    /**
     * If the owner document is a FluentDOM\DOM\Document register the namespace on the
     * document object, too.
     *
     * @param string $prefix
     * @param string $namespaceURI
     * @return bool
     * @throws \LogicException
     */
    public function registerNamespace($prefix, $namespaceURI): bool {
      if (
        $this->_documentReference instanceOf Document &&
        (
          !$this->_documentReference->namespaces()->offsetExists($prefix) ||
          $this->_documentReference->namespaces()->offsetGet($prefix) !== $namespaceURI
        )
      ) {
        $this->_documentReference->registerNamespace($prefix, $namespaceURI);
      }
      return parent::registerNamespace($prefix, $namespaceURI);
    }

    /**
     * Fetch nodes or scalar values from the DOM using Xpath expression.
     *
     * The main difference to DOMXpath::evaluate() is the handling of the third
     * argument. Namespace registration can be changed using the property and
     * is disabled by default.
     *
     * @param string $expression
     * @param \DOMNode|NULL $contextNode
     * @param NULL|bool $registerNodeNS
     * @return string|float|bool|\DOMNodeList
     */
    public function evaluate($expression, \DOMNode $contextNode = NULL, $registerNodeNS = NULL) {
      $registerNodeNS = $registerNodeNS ?? $this->registerNodeNamespaces;
      return parent::evaluate($expression, $contextNode, (bool)$registerNodeNS);
    }

    /**
     * Fetch nodes or scalar values from the DOM using Xpath expression.
     *
     * @param string $expression
     * @param \DOMNode|NULL $contextNode
     * @return string|float|bool|\DOMNodeList
     */
    public function __invoke(string $expression, \DOMNode $contextNode = NULL) {
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
     * @param \DOMNode|NULL $contextNode
     * @param NULL|bool $registerNodeNS
     * @return \DOMNodeList|NULL
     */
    public function query(
      $expression, \DOMNode $contextNode = NULL, $registerNodeNS = NULL
    ): ?\DOMNodeList {
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
     * @param \DOMNode|NULL $contextNode
     * @param NULL|bool $registerNodeNS
     * @return \DOMNode|NULL
     */
    public function firstOf(string $expression, \DOMNode $contextNode = NULL, bool $registerNodeNS = NULL): ?\DOMNode {
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
    public static function quote(string $string): string {
      $string = str_replace("\x00", '', $string);
      $hasSingleQuote = FALSE !== strpos($string, "'");
      if ($hasSingleQuote) {
        $hasDoubleQuote = FALSE !== strpos($string, '"');
        if ($hasDoubleQuote) {
          $result = '';
          preg_match_all('("[^\']*|[^"]+)', $string, $matches);
          foreach ($matches[0] as $part) {
            $quoteChar = 0 === strpos($part, '"') ? "'" : '"';
            $result .= ', '.$quoteChar.$part.$quoteChar;
          }
          return 'concat('.substr($result, 2).')';
        }
        return '"'.$string.'"';
      }
      return "'".$string."'";
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool {
      if ($name === 'registerNodeNamespaces') {
        return TRUE;
      }
      return isset($this->$name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
      if ($name === 'registerNodeNamespaces') {
        return $this->_registerNodeNamespaces;
      }
      return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) {
      if ($name === 'registerNodeNamespaces') {
        $this->_registerNodeNamespaces = (bool)$value;
        return;
      }
      $this->$name = $value;
    }

    /**
     * @param string $name
     */
    public function __unset(string $name) {
      if ($name === 'registerNodeNamespaces') {
        $this->registerNodeNamespaces = FALSE;
      }
      unset($this->$name);
    }
  }
}
