<?php
/**
* Interface for FluentDOM loaders
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Loaders
*/

/**
* Interface for FluentDOM loaders
*
* @package FluentDOM
* @subpackage Loaders
*/
interface FluentDOMLoader {

  /**
  * load FluentDOM document data from a source
  *
  * @param mixed $source
  * @param string $contentType
  * @return DOMDocument|array(DOMDocument,DOMNode)|FALSE
  */
  public function load($source, $contentType);
}

?>