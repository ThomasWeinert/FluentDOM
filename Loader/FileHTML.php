<?php
/**
* Load FluentDOM from XML file
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
require_once dirname(__FILE__).'/../FluentDOMLoader.php';

/**
* Load FluentDOM from XML file
*/
class FluentDOMLoaderFileHTML implements FluentDOMLoader {
  
  public function load($source, $contentType) {
    if (is_string($source) &&
        FALSE === strpos($source, '<') &&
        in_array($contentType, array('html', 'text/html'))) {
      $dom = new DOMDocument();
      $errorSetting = libxml_use_internal_errors(TRUE);
      libxml_clear_errors();
      $dom->loadHTMLFile($source);
      libxml_clear_errors();
      libxml_use_internal_errors($errorSetting);
      return $dom;
    }
    return FALSE;
  }
}

?>