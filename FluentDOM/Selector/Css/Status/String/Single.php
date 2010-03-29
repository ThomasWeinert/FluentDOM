<?php
/**
* FluentDOMSelectorCssStatusStringSingle checks for tokens in a single quoted string.
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Selector-CSS
*/

/**
* FluentDOMSelectorCssStatusStringSingle checks for tokens in a single quoted string.
*
* @package FluentDOM
* @subpackage Selector-CSS
*/
class FluentDOMSelectorCssStatusStringSingle implements FluentDOMSelectorStatus {

  /**
  * Try to get token in buffer at offset position.
  * 
  * @param string $buffer
  * @param integer $offset
  * @return FluentDOMSelectorCssToken
  */
  public function getToken($buffer, $offset) {
    if ("'" === substr($buffer, $offset, 1)) {
      return new FluentDOMSelectorCssToken(
        FluentDOMSelectorCSSToken::TOKEN_SINGLEQUOTE_STRING_END, "'", $offset
      );
    } else {
      $tokenString = substr($buffer, $offset, 2);
      if ("\\'" == $tokenString ||
          '\\\\' == $tokenString) {
        return new FluentDOMSelectorCssToken(
           FluentDOMSelectorCSSToken::TOKEN_STRING_ESCAPED_CHAR, $tokenString, $offset
        );
      } else {
        $tokenString = FluentDOMSelectorScanner::matchPattern(
          $buffer, $offset, '([^\\\\\']+)'
        );
        if (!empty($tokenString)) {
          return new FluentDOMSelectorCssToken(
            FluentDOMSelectorCSSToken::TOKEN_STRING_CHARS, $tokenString, $offset
          );
        }
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
    return (
      $token->type == FluentDOMSelectorCssToken::TOKEN_SINGLEQUOTE_STRING_END
    );
  }

  /**
  * Get new (sub)status if needed.
  * 
  * @param FluentDOMSelectorCssToken $token
  * @return FluentDOMSelectorStatus
  */
  public function getNewStatus($token) {
    return NULL;
  }
}