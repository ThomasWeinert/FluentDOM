<?php
/**
* FluentDOMStyle extends the FluentDOM class with a function to edit
* the style attribute of html tags
*
* @version $Id: FluentDOM.php 155 2009-06-11 13:08:01Z subjective $
*/

/**
* include the parant class (FluentDOM)
*/
require_once(dirname(__FILE__).'/FluentDOM.php');

/**
* Function to create a new FluentDOMStyle instance
*
* This is a shortcut for "new FluentDOMStyle($source)"
*
* @param mixed $source
* @access public
* @return object FluentDOMStyle
*/
function FluentDOMStyle($content) {
  return new FluentDOMStyle($content);
} 

/**
* FluentDOMStyle extends the FluentDOM class with a function to edit
* the style attribute of html tags
*/
class FluentDOMStyle extends FluentDOM {

  /**
  * redefine the _spawn() method to get an new instance of FluentDOMStyle
  *
  * @access protected
  * @return object FluentDOMStyle
  */
  protected function _spawn() {
    return new FluentDOMStyle($this);
  }
  
  /**
  * get or set CSS values in style attributes
  *
  * @param string | array $name
  * @param string | Closure $value
  * @access public
  * @return string | object FluentDOMStyle
  */
  public function css($name, $value) {
  
  }
}