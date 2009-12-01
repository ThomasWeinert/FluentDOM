<?php
/**
* FluentDOMSelectorStatus is the interface for FluentDOMSelectScanner status objects
*
* @version $Id: Iterator.php 345 2009-10-19 19:51:37Z subjective $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Selector
*/

/**
* FluentDOMSelectorStatus is the interface for FluentDOMSelectScanner status objects.
*
* @package FluentDOM
* @subpackage Selector
*/
interface FluentDOMSelectorStatus {

  /**
  * Try to get token in buffer at offset position.
  * 
  * @param string $buffer
  * @param integer $offset
  * @return FluentDOMSelectorToken
  */
  public function getToken($buffer, $offset);

  /**
  * Check if token ends status
  * 
  * @param FluentDOMSelectorToken $token
  * @return boolean
  */
  public function isEndToken($token);

  /**
  * Get new (sub)status if needed.
  * 
  * @param FluentDOMSelectorToken $token
  * @return FluentDOMSelectorStatus
  */
  public function getNewStatus($token);
}