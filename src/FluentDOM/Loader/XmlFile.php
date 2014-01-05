<?php
/**
 * Load a DOM document from a xml file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\LoaderInterface;

  /**
   * Load a DOM document from a xml file
   */
  class XmlFile implements LoaderInterface {

    /**
     * @see LoaderInterface::supports
     * @param string $contentType
     * @return bool
     */
    public function supports($contentType) {
      switch ($contentType) {
      case 'text/xml' :
        return TRUE;
      }
      return FALSE;
    }

    /**
     * @see LoaderInterface::load
     * @param string $source
     * @return bool
     */
    public function load($source, $contentType = 'text/xml') {
      if ($this->supports($contentType) &&
          0 !== strpos($source, '<')) {
        $dom = new Document();
        $dom->load($source);
        return $dom;
      }
      return NULL;
    }

  }
}