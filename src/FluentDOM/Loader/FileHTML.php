<?php
/**
* Load FluentDOM from local HTML file
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
require_once(dirname(__FILE__).'/../Loader.php');

/**
* Load FluentDOM from HTML file
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderFileHTML implements FluentDOMLoader {

  /**
  * load DOMDocument from local HTML file
  *
  * @param string $source filename
  * @param string $contentType
  * @return DOMDocument|FALSE
  */
  public function load($source, &$contentType) {
    if (is_string($source) &&
        FALSE === strpos($source, '<') &&
        $contentType == 'text/html') {

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