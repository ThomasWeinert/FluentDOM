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
* @example json/jsonToXml.php Usage Example: FluentDOMLoaderStringJSON
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderStringJSON implements FluentDOMLoader {

  /**
  * JSON errors
  * @var array $jsonErrors
  */
  private $jsonErrors = array(
    -1 => 'Unknown error has occurred',
    0 => 'No error has occurred',
    1 => 'The maximum stack depth has been exceeded',
    3 => 'Control character error, possibly incorrectly encoded',
    4 => 'Syntax error',
  );

  /**
  * Add variable type attributes to the element nodes
  * @var string
  */
  public $typeAttributes = FALSE;

  /**
  * Load DOMDocument from local XML file
  *
  * @param string $source json encoded content
  * @param string $contentType
  * @return array(DOMDocument,DOMNode)|FALSE
  */
  public function load($source, &$contentType) {
    if (is_string($source)) {
      $firstChar = substr(trim($source), 0, 1);
      if (in_array($firstChar, array('{', '['))) {
        $contentType = 'text/xml';
        $json = json_decode($source);
        if ($json) {
          $dom = new DOMDocument();
          $documentElement = $dom->createElement('json');
          $dom->appendChild($documentElement);
          $this->_toDom($documentElement, $json);
          return array($dom, array($documentElement));
        } else {
          $code = is_callable('json_last_error') ? json_last_error() : -1;
          throw new UnexpectedValueException($this->jsonErrors[$code]);
        }
      }
    }
    return FALSE;
  }

  /**
  * Convert a JSON object structure to a DOMDocument
  *
  * @param DOMElement $parentNode
  * @param mixed $current
  * @param integer $maxDepth simple recursion protection
  */
  private function _toDom($parentNode, $current, $maxDepth = 100) {
    if (is_array($current) && $maxDepth > 0) {
      foreach ($current as $index => $child) {
        $childNode = $this->_addElement($parentNode, $parentNode->tagName.'-child');
        $this->_toDom($childNode, $child, $maxDepth - 1);
      }
    } elseif (is_object($current) && $maxDepth > 0) {
      foreach (get_object_vars($current) as $index => $child) {
        $childNode = $this->_addElement($parentNode, $index);
        $this->_toDom($childNode, $child, $maxDepth - 1);
      }
    } elseif (is_bool($current)) {
      $parentNode->appendChild(
        $parentNode->ownerDocument->createTextNode($current ? '1' : '0')
      );
    } elseif (!empty($current)) {
      $parentNode->appendChild(
        $parentNode->ownerDocument->createTextNode((string)$current)
      );
    }
    if ($this->typeAttributes) {
      $parentNode->setAttribute('type', gettype($current));
    }
  }

  /**
  * Add new element, sanitize tag name if nessesary
  *
  * @param DOMElement $parentNode
  * @param string $tagName
  */
  private function _addElement($parentNode, $tagName) {
    $nameStartChar =
       'A-Z_a-z'.
       '\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}'.
       '\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}'.
       '\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}'.
       '\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}';
    $nameChar =
       $nameStartChar.
       '\\.\\d\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}';
    $tagNameNormalized = preg_replace(
      '((^[^'.$nameStartChar.'])|[^'.$nameChar.'])u', '-', $tagName
    );
    $childNode = $parentNode->ownerDocument->createElement($tagNameNormalized);
    if ($tagNameNormalized != $tagName) {
      $childNode->setAttribute('name', $tagName);
    }
    $parentNode->appendChild($childNode);
    return $childNode;
  }
}

?>