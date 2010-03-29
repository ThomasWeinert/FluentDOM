<?php
/**
* FluentDOMSelectorCssToken represents a token from a scan.
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Selector-CSS
*/

/**
* FluentDOMSelectorCssToken represents a token from a scan.
*
* @package FluentDOM
* @subpackage Selector-CSS
*/
class FluentDOMSelectorCssToken extends FluentDOMSelectorToken {

  //whitespace
  const TOKEN_WHITESPACE = 0;

  //selectors
  const TOKEN_TYPE_SELECTOR = 1;
  const TOKEN_CLASS_SELECTOR = 2;
  const TOKEN_ID_SELECTOR = 3;
  const TOKEN_PSEUDO_CLASS = 4;

  // attribute conditions - [...]
  const TOKEN_ATTRIBUTES_SELECTOR_START = 20;
  const TOKEN_ATTRIBUTES_SELECTOR_END = 21;
  const TOKEN_ATTRIBUTE_NAME = 22;

  // pseudo class parameters - (...)
  const TOKEN_PARAMETERS_START = 31;
  const TOKEN_PARAMETERS_END = 32;

  //selector separator
  const TOKEN_SELECTOR_SEPARATOR = 41;

  //single quoted strings
  const TOKEN_SINGLEQUOTE_STRING_START = 100;
  const TOKEN_SINGLEQUOTE_STRING_END = 101;
  // double quoted strings
  const TOKEN_DOUBLEQUOTE_STRING_START = 102;
  const TOKEN_DOUBLEQUOTE_STRING_END = 103;
  // string general
  const TOKEN_STRING_CHARS = 110;
  const TOKEN_STRING_ESCAPED_CHAR = 112;

  /**
  * Convert token object to string
  * @return string
  */
  public function __toString() {
    return 'CSS_TOKEN#'.$this->type.':'.$this->position.' '.self::quoteContent($this->content);
  }
}

?>