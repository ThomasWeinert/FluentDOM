<?php
/**
* Load FluentDOM from SimpleXMLElement
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
require_once dirname(__FILE__).'/../Loader.php';

/**
* Load FluentDOM from SimpleXMLElement
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderSimpleXMLElement implements FluentDOMLoader {

  /**
  * Select DOMNode representation of an existing SimpleXMLElement
  *
  * @param SimpleXMLElement $source
  * @param string $contentType
  * @access public
  * @return array(DOMDocument,DOMNode)|FALSE
  */
  public function load($source, $contentType) {
    if ($source instanceof SimpleXMLElement) {
      $node = dom_import_simplexml($source);
      return array($node->ownerDocument, array($node));
    }
    return FALSE;
  }
}

?>