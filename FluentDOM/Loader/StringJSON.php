<?php
/**
* Load FluentDOM from JSON encoded string
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Loaders
*/

/**
* include interface
*/
require_once(dirname(__FILE__).'/../Loader.php');

/**
* Load FluentDOM from JSON encoded string
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderStringJSON implements FluentDOMLoader {

  private $jsonErrors = array(
    JSON_ERROR_NONE => 'No error has occurred',
    JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
    JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
    JSON_ERROR_SYNTAX => 'Syntax error',
  );

  /**
  * load DOMDocument from local XML file
  *
  * @param string $source json encoded content
  * @param string $contentType
  * @access public
  * @return DOMElement|FALSE
  */
  public function load($source, $contentType) {
    if (is_string($source) &&
        $contentType == 'application/json') {
      $json = json_decode($source);
      if ($json) {
        $dom = new DOMDocument();
        $documentElement = $dom->createElement('json');
        $dom->appendChild($documentElement);
        $this->_toDom($documentElement, $json);
        return array($dom, array($documentElement));
      } else {
        throw new UnexpectedValueException($this->jsonErrors[json_last_error()]);
      }
    }
    return FALSE;
  }

  /**
  * Convert a JSON object structure to a DOMDocument
  * @param DOMElement $parentNode
  * @param mixed $current
  * @param integer $maxDepth simple recursion protection
  */
  private function _toDom($parentNode, $current, $maxDepth = 100) {
    if (is_array($current) && $maxDepth > 0) {
      foreach ($current as $index => $child) {
        $childNode = $parentNode->ownerDocument->createElement($parentNode->tagName.'-child');
        $parentNode->appendChild($childNode);
        $this->_toDom($childNode, $child, $maxDepth - 1);
      }
    } elseif (is_object($current) && $maxDepth > 0) {
      foreach (get_object_vars($current) as $index => $child) {
        $childNode = $parentNode->ownerDocument->createElement($index);
        $parentNode->appendChild($childNode);
        $this->_toDom($childNode, $child, $maxDepth - 1);
      }
    } elseif (is_bool($current)) {
      $parentNode->appendChild(
        $parentNode->ownerDocument->createTextNode($current ? '1' : '0')
      );
    } else {
      $parentNode->appendChild(
        $parentNode->ownerDocument->createTextNode((string)$current)
      );
    }
  }
}

?>