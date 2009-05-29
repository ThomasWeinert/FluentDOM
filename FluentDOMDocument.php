<?php
/**
* DOMDocument with integrated XPath and a fluent interface
* 
* @version $Id: FluentDOMDocument.php,v 0.0 00.00.0000 00:00:00 weinert Exp $
*/

/**
* include the FluentDOMNodeList class
*/
require_once(dirname(__FILE__).'/FluentDOMNodeList.php');

/**
* DOMDocument with integrated XPath and a fluent interface
*/
class FluentDOMDocument extends DOMDocument {

  public function __construct($version = NULL, $encoding = NULL) {
    parent::__construct($version, $encoding);
  }
  
  /**
  * Get a DOMXPath instance for the document.
  * 
  * The document changes if content is loaded and the connection to the DOMXPath object is lost.
  * The DOMXPath object is recreated if it's document property does not match $this.
  *
  * @access private
  * @return object DOMXPath
  */
  private function xpath() {
    static $xpath;
    if (empty($xpath) || $xpath->document != $this) {
      $xpath = new DOMXPath($this);
    }
    return $xpath;
  }
  
  /**
  * Find nodes matching the given xpath expression.
  *
  * @param string $expr xpath expression
  * @param mixed $context optional, default value NULL
  * @access public
  * @return object FluentDOMNodeList
  */
  public function find($expr, $context = NULL) {
    if (empty($context)) {
      $result = new FluentDOMNodeList($this);
      foreach ($this->xpath()->query($expr) as $node) {
        $result->add($node);
      }
    } elseif ($context instanceof FluentDOMNodeList) {
      $result = new FluentDOMNodeList($this, $context);
      foreach ($context as $contextNode) {
        foreach ($this->xpath()->query($expr, $contextNode) as $node) {
          $result->add($node);
        }
      }
    } elseif ($context instanceof DOMElement) {
      $result = new FluentDOMNodeList($this);
      foreach ($this->xpath()->query($expr, $context) as $node) {
        $result->add($node);
      }
    } else {
      $result = new FluentDOMNodeList($this);
    }
    return $result;
  }
}

?>