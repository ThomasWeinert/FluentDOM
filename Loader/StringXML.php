<?php
/**
* Load FluentDOM from XML string
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage Loaders
*/

/**
* include interface
*/
require_once dirname(__FILE__).'../FluentDOMLoader.php';

/**
* Load FluentDOM from XML string
*/
class FluentDOMLoaderStringXML implements FluentDOMLoader {
  
  public function load($source, $contentType) {
    if (is_string($source) &&
        FALSE !== strpos($source, '<') &&
        in_array($contentType, array('xml', 'text/xml'))) {
      $dom = new DOMDocument();
      $dom->loadXML($source);
      return $dom;
    }
    return FALSE;
  }
}

?>