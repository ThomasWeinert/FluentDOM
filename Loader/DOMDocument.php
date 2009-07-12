<?php
/**
* Load FluentDOM from DOMDocument
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @package Loaders
*/

/**
* Load FluentDOM from DOMDocument
*/
class FluentDOMLoaderDOMDocument implements FluentDOMLoader {
  
  public function load($source, $contentType) {
    if ($source instanceof DOMDocument) { 
      return $source;
    }
    return FALSE;
  }
}

?>