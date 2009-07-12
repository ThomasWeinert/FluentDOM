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
class FluentDOMLoaderFileXML implements FluentDOMLoader {

  public function load($source, $contentType) {
    if (is_string($source) &&
        FALSE === strpos($source, '<') &&
        in_array($contentType, array('xml', 'text/xml'))) {

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