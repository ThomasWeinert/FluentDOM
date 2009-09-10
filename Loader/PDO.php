<?php
/**
* Load FluentDOM from pdo result
*
* @version $Id: StringXML.php 305 2009-07-24 18:03:59Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Loaders
*/

/**
* include interface
*/
require_once dirname(__FILE__).'/../FluentDOMLoader.php';

/**
* Load FluentDOM from pdo result
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderPDO implements FluentDOMLoader {
  
  protected $_tagNameRoot = 'records';
  protected $_tagNameRecord = 'record';
  protected $_tagNameColumn = 'column';
  
  /**
  * Load DOMDocument from xml string
  *
  * @param string $source xml string
  * @param string $contentType
  * @access public
  * @return object DOMDocument | FALSE
  */
  public function load($source, $contentType) {
    if (is_object($source) &&
        $source instanceof PDOStatement) {
      $source->setFetchMode(PDO::FETCH_ASSOC);
      $dom = new DOMDocument();
      $dom->formatOutput = TRUE;
      $dom->appendChild(
        $rootNode = $dom->createElement($this->_tagNameRoot)
      );
      foreach ($source as $row) {
        $rootNode->appendChild(
          $recordNode = $dom->createElement($this->_tagNameRecord)
        );
        foreach ($row as $name => $value) {
          $recordNode->appendChild(
            $valueNode = $dom->createElement($this->_tagNameColumn, $value)
          );
          $valueNode->setAttribute('name', $name);
        }
      }
      return $dom;
    }
    return FALSE;
  }
}

?>