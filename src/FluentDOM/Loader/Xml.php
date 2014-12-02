<?php
/**
 * Load a DOM document from a xml file or string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\Loadable;

  /**
   * Load a DOM document from a xml file or string
   */
  class Xml implements Loadable {

    use Supports;

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('xml', 'application/xml', 'text/xml');
    }

    /**
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if ($this->supports($contentType)) {
        $dom = new Document();
        $dom->preserveWhiteSpace = FALSE;
        if ($this->startsWith($source, '<')) {
          $dom->loadXml($source);
        } else {
          $dom->load($source);
        }
        return $dom;
      }
      return NULL;
    }

  }
}