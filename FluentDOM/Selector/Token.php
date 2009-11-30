<?php
/**
* FluentDOMSelectorToken represents a token from a scan.
*
* @version $Id: Iterator.php 345 2009-10-19 19:51:37Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Selector
*/

/**
* FluentDOMSelectorToken represents a token from a scan.
*
* @package FluentDOM
* @subpackage Selector
*/
class FluentDOMSelectorToken {

  /**
  * Token type
  * @var integer
  */
  private $_type = NULL;
  /**
  * Token string content
  * @var string
  */
  private $_content = NULL;
  /**
  * Token string content length
  * @var integer
  */
  private $_length = 0;
  /**
  * Byte position the token was found at
  * @var integer
  */
  private $_position = 0;

  /**
  * Construct and initialize token
  * @param integer $type
  * @param string $content
  * @param integer $position
  * @return FluentDOMSelectorToken
  */
  public function __construct($type = 0, $content = '', $position = -1) {
    $this->_type = $type;
    $this->_content = $content;
    $this->_length = strlen($content);
    $this->_position = $position;
  }

  /**
  * Get token attribute
  * @param string $name
  */
  public function __get($name) {
    switch ($name) {
    case 'type' :
      return $this->_type;
    case 'content' :
      return $this->_content;
    case 'length' :
      return $this->_length;
    case 'position' :
      return $this->_position;
    }
    throw new InvalidArgumentException();
  }

  /**
  * Do not allow to set attributes
  * @param string $name
  * @param mixed $value
  * @return void
  */
  public function __set($name, $value) {
    throw new BadMethodCallException();
  }

  /**
  * Convert token object to string
  * @return string
  */
  public function __toString() {
    return 'TOKEN#'.$this->type.':'.$this->position.' '.self::quoteContent($this->content);
  }

  /**
  * Escape content for double quoted, single line string representation
  * @param string $content
  * @return string
  */
  public static function quoteContent($content) {
    return "'".str_replace(
      array('\\', "\r", "\n", "'"),
      array('\\\\', '\\r', '\\n', "\\'"),
      (string)$content
    )."'";
  }
}