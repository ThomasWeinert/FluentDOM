<?php
/**
 * Load a DOM document from a xml file or string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Loadable;

  /**
   * Load a DOM document from a xml file or string
   */
  class Xml implements Loadable {

    use Supports\Libxml;

    const LIBXML_OPTIONS = 'libxml';

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
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, $options = []) {
      if ($this->supports($contentType)) {
        return $this->loadXmlDocument($source, $contentType, $options);
      }
      return NULL;
    }

    /**
     * @see LoadableFragment::loadFragment
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment($source, $contentType, $options = []) {
      if ($this->supports($contentType)) {
        return (new Libxml\Errors())->capture(
          function() use ($source, $contentType, $options) {
            $document = new Document();
            $fragment = $document->createDocumentFragment();
            $fragment->appendXml($source);
            return $fragment;
          }
        );
      }
      return NULL;
    }
  }
}