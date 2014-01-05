<?php
/**
 * Load a DOM document from a xml string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\LoaderInterface;

  /**
   * Load a DOM document from a xml string
   */
  class HtmlString implements LoaderInterface {

    /**
     * @see LoaderInterface::supports
     * @param string $contentType
     * @return bool
     */
    public function supports($contentType) {
      switch ($contentType) {
      case 'text/html' :
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
          0 === strpos($source, '<')) {
        $dom = new Document();
        $errorSetting = libxml_use_internal_errors(TRUE);
        libxml_clear_errors();
        $dom->loadHTML($source);
        libxml_clear_errors();
        libxml_use_internal_errors($errorSetting);
        return $dom;
      }
      return NULL;
    }

  }
}