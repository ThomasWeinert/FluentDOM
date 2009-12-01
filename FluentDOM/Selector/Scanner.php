<?php
/**
* FluentDOMSelectorScanner scans a selector string for tokens.
*
* @version $Id: Iterator.php 345 2009-10-19 19:51:37Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Selector
*/

/**
* Include status interface
*/
require_once(dirname(__FILE__).'/Status.php');
/**
* Include token class
*/
require_once(dirname(__FILE__).'/Token.php');

/**
* FluentDOMSelectorScanner scans a selector string for tokens.
*
* @package FluentDOM
* @subpackage Selector
*/
class FluentDOMSelectorScanner {

  /**
  * Scanner status object
  * @var FluentDOMSelectorStatus
  */
  private $_status = NULL;
  /**
  * selector buffer
  * @var string
  */
  private $_buffer = '';
  /**
  * current offset
  * @var interger
  */
  private $_offset = 0;

  /**
  * Constructor, set status object
  */
  public function __construct(FluentDOMSelectorStatus $status) {
    $this->_status = $status;
  }

  /**
  * Scan selector string for tokens
  * 
  * @param array $target token target
  * @param string $selector selector string
  * @param integer $offset start offset
  * @return integer new offset
  */
  public function scan(&$target, $selector, $offset = 0) {
    $this->_buffer = $selector;
    $this->_offset = $offset;
    while ($token = $this->_next()) {
      $target[] = $token;
      // switch back to previous scanner
      if ($this->_status->isEndToken($token)) {
        return $this->_offset;
      }
      // check for status switch
      if ($newStatus = $this->_status->getNewStatus($token)) {
        // delegate to subscanner
        $this->_offset = $this->_delegate($target, $newStatus);
      }
    }
    if ($this->_offset < strlen($this->_buffer)) {
      throw new UnexpectedValueException(
        sprintf(
          'Invalid char "%s" for status "%s" at offset #%d in selector "%s"',
          substr($this->_buffer, $this->_offset, 1),
          get_class($this->_status),
          $this->_offset,
          $this->_buffer
        )
      );
    }
    return $this->_offset;
  }

  /**
  * Get next token
  * 
  * @return FluentDOMSelectorToken|NULL
  */
  private function _next() {
    if (($token = $this->_status->getToken($this->_buffer, $this->_offset)) &&
        $token->length > 0) {
      $this->_offset += $token->length;
      return $token;
    }
    return NULL;
  }

  /**
  * Delegate to subscanner
  * 
  * @param array $target
  * @param FluentDOMSelectorStatus $status
  * @return unknown_type
  */
  private function _delegate(&$target, $status) {
    $scanner = new self($status);
    return $scanner->scan($target, $this->_buffer, $this->_offset);
  }

  /**
  * Match pattern against buffer string
  *
  * @param string $buffer
  * @param integer $offset
  * @param string $pattern
  * @return string|NULL
  */
  public static function matchPattern($buffer, $offset, $pattern) {
    $found = preg_match(
      $pattern, $buffer, $match, PREG_OFFSET_CAPTURE, $offset
    );
    if ($found &&
        isset($match[0]) &&
        isset($match[0][1]) &&
        $match[0][1] === $offset) {
      return $match[0][0];
    }
    return NULL;
  }
}

?>