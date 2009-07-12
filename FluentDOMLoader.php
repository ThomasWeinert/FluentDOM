<?php
/**
* Interface for FluentDOM loaders
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @package Loaders
*/

/**
* Interface for FluentDOM loaders
*/
interface FluentDOMLoader {
  public function load($source, $contentType);
}

?>