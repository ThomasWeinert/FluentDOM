<?php
/**
 * Load a DOM document from a xml file or string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
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
    public function getSupported(): array {
      return ['xml', 'application/xml', 'text/xml'];
    }

    /**
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     * @throws \FluentDOM\Exceptions\InvalidSource\TypeString
     * @throws \FluentDOM\Exceptions\InvalidSource\TypeFile
     */
    public function load($source, string $contentType, $options = []) {
      if ($this->supports($contentType)) {
        return $this->loadXmlDocument($source, $options);
      }
      return NULL;
    }

    /**
     * @see LoadableFragment::loadFragment
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     * @throws \InvalidArgumentException
     */
    public function loadFragment($source, string $contentType, $options = []) {
      if ($this->supports($contentType)) {
        return (new Libxml\Errors())->capture(
          function() use ($source) {
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