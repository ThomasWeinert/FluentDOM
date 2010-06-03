<?php
/**
* Load FluentDOM from local XML file
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
* Load FluentDOM from XML file
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderFileXML implements FluentDOMLoader {

  /**
  * load DOMDocument from local XML file
  *
  * @param string $source filename
  * @param string $contentType
  * @return DOMDocument|FALSE
  */
  public function load($source, &$contentType) {
    if (is_string($source) &&
        FALSE === strpos($source, '<') &&
        $contentType == 'text/xml') {

      if (!file_exists($source)) {
        throw new InvalidArgumentException('File not found: '. $source);
      }

      $dom = new DOMDocument();
      $dom->load($source);
      return $dom;
    }
    return FALSE;
  }
}

?>