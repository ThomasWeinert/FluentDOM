<?php
/**
 * Load a DOM document from a xml string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\Loadable;

  /**
   * Load a DOM document from a xml string
   */
  class XmlString implements Loadable {

    /**
     * @see Loadable::supports
     * @param string $source
     * @return bool
     */
    public function supports($contentType) {
      switch ($contentType) {
      case 'xml' :
      case 'application/xml' :
      case 'text/xml' :
        return TRUE;
      }
      return FALSE;
    }

    /**
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @return Document|NULL
     */
    public function load($source, $contentType) {
      if ($this->supports($contentType) &&
          0 === strpos($source, '<')) {
        $dom = new Document();
        $dom->loadXML($source);
        return $dom;
      }
      return NULL;
    }

  }
}