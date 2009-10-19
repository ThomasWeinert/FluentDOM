<?php
/**
* Load FluentDOM from DOMDocument
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
* Load FluentDOM from DOMDocument
*
* @package FluentDOM
* @subpackage Loaders
*/
class FluentDOMLoaderDOMDocument implements FluentDOMLoader {

  /**
  * Attach existing DOMDocument to a FluentDOM
  *
  * @param DOMDocument $source
  * @param string $contentType
  * @access public
  * @return DOMDocument|FALSE
  */
  public function load($source, $contentType) {
    if ($source instanceof DOMDocument) {
      return $source;
    }
    return FALSE;
  }
}

?>