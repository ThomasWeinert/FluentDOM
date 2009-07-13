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
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderFileHTML implements FluentDOMLoader {

  /**
  * load DOMDocument from html file
  *
  * @param string $source filename
  * @param string $contentType
  * @access public
  * @return object DOMDocument | FALSE
  */
  public function load($source, $contentType) {
    if (is_string($source) &&
        FALSE === strpos($source, '<') &&
        in_array($contentType, array('html', 'text/html'))) {

      if (!file_exists($source)) {
        throw new InvalidArgumentException('File not found: '. $source);
      }

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