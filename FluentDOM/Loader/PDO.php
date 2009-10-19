<?php
/**
* Load FluentDOM from pdo result
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
require_once dirname(__FILE__).'/../Loader.php';

/**
* Load FluentDOM from pdo result
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderPDO implements FluentDOMLoader {

  /**
  * Element type value
  * @var integer
  */
  const ELEMENT_VALUE = 1;
  /**
  * Element type attribute
  * @var integer
  */
  const ATTRIBUTE_VALUE = 2;

  /**
  * root element name
  * @var string
  */
  protected $_tagNameRoot = 'records';
  /**
  * record element name 
  * @var unknown_type
  */
  protected $_tagNameRecord = 'record';

  /**
  * set root and record tag name for xml elements.
  *
  * @param string $root
  * @param string $record
  */
  public function setTagNames($root, $record) {
    $this->_tagNameRoot = $root;
    $this->_tagNameRecord = $record;
  }

  /**
  * Load DOMDocument from xml string
  *
  * @param string $source xml string
  * @param string $contentType
  * @access public
  * @return DOMDocument|FALSE
  */
  public function load($source, $contentType) {
    if (is_object($source) &&
        $source instanceof PDOStatement) {
      $source->setFetchMode(PDO::FETCH_NUM);
      $columnCount = $source->columnCount();
      $columns = array();
      for ($i = 0; $i < $columnCount; $i++) {
        $columnData = $source->getColumnMeta($i);
        $columns[$i] = array(
          'name' => $this->_normalizeColumnName($columnData['name']),
          'type' => $this->_getNodeType($columnData)
        );
      }
      $dom = new DOMDocument();
      $dom->formatOutput = TRUE;
      $dom->appendChild(
        $rootNode = $dom->createElement($this->_tagNameRoot)
      );
      foreach ($source as $row) {
        $rootNode->appendChild(
          $recordNode = $dom->createElement($this->_tagNameRecord)
        );
        foreach ($row as $columnId => $value) {
          switch ($columns[$columnId]['type']) {
          case self::ATTRIBUTE_VALUE :
            $recordNode->setAttribute(
              $columns[$columnId]['name'],
              $value
            );
            break;
          default :
            $recordNode->appendChild(
              $valueNode = $dom->createElement(
                $columns[$columnId]['name'],
                $value
              )
            );
            break;
          }
        }
      }
      return $dom;
    }
    return FALSE;
  }

  /**
  * normalize column for tag name use
  *
  * @access protected
  * @param string $name
  */
  protected function _normalizeColumnName($name) {
    return preg_replace('([:|+~\s]+)', '-', $name);
  }

  /**
  * get node type (attribute or element)
  *
  * @access protected
  * @param array $columnData
  */
  protected function _getNodeType($columnData) {
    if ($columnData['native_type'] == 'string') {
      return self::ELEMENT_VALUE;
    } else {
      return self::ATTRIBUTE_VALUE;
    }
  }
}

?>