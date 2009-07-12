<?php
/**
* Load FluentDOM from XML file
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @package Loaders
*/

/**
* Load FluentDOM from XML file
*/
class FluentDOMLoaderFileXML implements FluentDOMLoader {
  
  public function load($source, $contentType) {
    if (is_string($source) &&
        FALSE === strpos($source, '<') &&
        in_array($contentType, array('xml', 'text/xml'))) {
      $dom = new DOMDocument();
      $dom->load($source);
      return $dom;
    }
    return FALSE;
  }
}

?>