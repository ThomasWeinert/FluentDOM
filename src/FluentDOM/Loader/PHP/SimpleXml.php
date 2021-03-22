<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader\PHP {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Result;
  use FluentDOM\Loader\Supports;
  use FluentDOM\Loader\Xml;

  /**
   * Load a DOM document from a SimpleXML element
   */
  class SimpleXml implements Loadable {

    use Supports;
    public const CONTENT_TYPES = ['simplexml', 'php/simplexml'];

    /**
     * @var Xml|NULL
     */
    private $_xmlLoader;

    /**
     * @see Loadable::load
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     */
    public function load($source, string $contentType, $options = []): ?Result {
      if ($source instanceof \SimpleXMLElement) {
        $document = new Document();
        $document->appendChild($document->importNode(\dom_import_simplexml($source), TRUE));
        return new Result($document, 'text/xml');
      }
      return NULL;
    }

    /**
     * @see Loadable::loadFragment
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     * @throws \Throwable
     */
    public function loadFragment($source, string $contentType, $options = []): ?DocumentFragment {
      if (!$this->supports($contentType)) {
        return NULL;
      }
      if (\is_string($source)) {
        $this->_xmlLoader = $this->_xmlLoader ?: new Xml();
        return $this->_xmlLoader->loadFragment($source, 'text/xml');
      }
      if ($source instanceof \SimpleXMLElement) {
        $node = \dom_import_simplexml($source);
        $document = new Document();
        $fragment = $document->createDocumentFragment();
        $fragment->appendChild($document->importNode($node, TRUE));
        return $fragment;
      }
      throw new InvalidArgument('source', [\SimpleXMLElement::class, 'string']);
    }
  }
}
