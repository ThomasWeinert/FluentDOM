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

    use Supports;

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
     * @param array $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if ($this->supports($contentType)) {
        $dom = new Document();
        $dom->preserveWhiteSpace = FALSE;
        $loadOptions = isset($options[self::LIBXML_OPTIONS]) ? (int)$options[self::LIBXML_OPTIONS] : 0;
        if ($this->startsWith($source, '<')) {
          $dom->loadXml($source, $loadOptions);
        } else {
          $dom->load($source, $loadOptions);
        }
        return $dom;
      }
      return NULL;
    }

    /**
     * @see LoadableFragment::loadFragment
     * @param string $source
     * @param string $contentType
     * @param array $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment($source, $contentType, array $options = []) {
      if ($this->supports($contentType)) {
        $dom = new Document();
        $fragment = $dom->createDocumentFragment();
        $fragment->appendXml($source);
        return $fragment;
      }
      return NULL;
    }
  }
}