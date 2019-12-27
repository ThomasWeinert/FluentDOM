<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

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
    const CONTENT_TYPES = ['xml', 'application/xml', 'text/xml'];

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
