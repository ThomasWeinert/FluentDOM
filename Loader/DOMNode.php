<?php
/**
* Load FluentDOM from DOMNode
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
* Load FluentDOM from DOMDocument
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderDOMNode implements FluentDOMLoader {

  /**
  * attach existing DOMNode->ownerdocument and select the DOMNode
  *
  * @param DOMNode $source
  * @param string $contentType
  * @access public
  * @return array(DOMDocument,DOMNode)|FALSE
  */
  public function load($source, $contentType) {
    if ($source instanceof DOMNode && !empty($source->ownerDocument)) {
      return array($source->ownerDocument, array($source));
    }
    return FALSE;
  }
}

?>