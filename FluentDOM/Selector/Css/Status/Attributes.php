<?php
/**
* FluentDOMSelectorCssStatusDefault checks for tokens css selector string.
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Selector-CSS
*/

/**
* FluentDOMSelectorCssStatusDefault checks for tokens css selector string.
*
* @package FluentDOM
* @subpackage Selector-CSS
*/
class FluentDOMSelectorCssStatusAttributes implements FluentDOMSelectorStatus {

  /**
  * single char tokens
  * @var array
  */
  protected $_tokenChars = array(
    FluentDOMSelectorCssToken::TOKEN_ATTRIBUTES_SELECTOR_END => ']'
  );

  /**
  * patterns for more complex tokens
  * @var array
  */
  protected $_tokenPatterns = array(
    FluentDOMSelectorCssToken::TOKEN_WHITESPACE => '([\r\n\t ]+)',
    FluentDOMSelectorCssToken::TOKEN_ATTRIBUTE_NAME => '([a-z]+)'
  );

  /**
  * Try to get token in buffer at offset position.
  * 
  * @param string $buffer
  * @param integer $offset
  * @return FluentDOMSelectorCssToken
  */
  public function getToken($buffer, $offset) {
    $char = substr($buffer, $offset, 1);
    foreach ($this->_tokenChars as $type => $expectedChar) {
      if ($char === $expectedChar) {
        return new FluentDOMSelectorCssToken(
          $type, $char, $offset
        );
      }
    }
    foreach ($this->_tokenPatterns as $type => $pattern) {
      $tokenString = FluentDOMSelectorScanner::matchPattern(
        $buffer, $offset, $pattern
      );
      if (!empty($tokenString)) {
        return new FluentDOMSelectorCssToken(
          $type, $tokenString, $offset
        );
      }
    }
    return NULL;
  }

  /**
  * Check if token ends status
  * 
  * @param FluentDOMSelectorCssToken $token
  * @return boolean
  */
  public function isEndToken($token) {
    return $token->type == FluentDOMSelectorCssToken::TOKEN_ATTRIBUTES_SELECTOR_END;
  }

  /**
  * Get new (sub)status if needed.
  * 
  * @param FluentDOMSelectorCssToken $token
  * @return FluentDOMSelectorStatus
  */
  public function getNewStatus($token) {
    switch ($token->type) {
    case FluentDOMSelectorCssToken::TOKEN_SINGLEQUOTE_STRING_START :
      return new FluentDOMSelectorCssStatusStringSingle();
    case FluentDOMSelectorCssToken::TOKEN_DOUBLEQUOTE_STRING_START :
      return new FluentDOMSelectorCssStatusStringDouble();
    }
    return NULL;
  }
}