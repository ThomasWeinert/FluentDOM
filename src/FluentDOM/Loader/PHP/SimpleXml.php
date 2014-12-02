<?php
/**
 * Load a DOM document from a SimpleXML element
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\PHP {

  use FluentDOM\Document;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;

  /**
   * Load a DOM document from a SimpleXML element
   */
  class SimpleXml implements Loadable {

    use Supports;

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('php/simplexml', 'simplexml');
    }

    /**
     * @see Loadable::load
     * @param \SimpleXMLElement $source
     * @param string $contentType
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if ($source instanceof \SimpleXMLElement) {
        $dom = new Document();
        $dom->appendChild($dom->importNode(dom_import_simplexml($source), TRUE));
        return $dom;
      }
      return NULL;
    }
  }
}