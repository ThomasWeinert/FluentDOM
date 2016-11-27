<?php
/**
 * Load a DOM document from a SimpleXML element
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\PHP {

  use Doctrine\Instantiator\Exception\InvalidArgumentException;
  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\Exceptions\InvalidFragmentLoader;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Result;
  use FluentDOM\Loader\Supports;
  use FluentDOM\Loader\Xml;

  /**
   * Load a DOM document from a SimpleXML element
   */
  class SimpleXml implements Loadable {

    use Supports;

    /**
     * @var Xml|null
     */
    private $_xmlLoader = NULL;

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('simplexml', 'php/simplexml');
    }

    /**
     * @see Loadable::load
     * @param \SimpleXMLElement $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, $options = []) {
      if ($source instanceof \SimpleXMLElement) {
        $document = new Document();
        $document->appendChild($document->importNode(dom_import_simplexml($source), TRUE));
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
     */
    public function loadFragment($source, $contentType, $options = []) {
      if (!$this->supports($contentType)) {
        return NULL;
      } elseif (is_string($source)) {
        $this->_xmlLoader = $this->_xmlLoader ?: new Xml();
        return $this->_xmlLoader->loadFragment($source, 'text/xml');
      } elseif ($source instanceof \SimpleXMLElement) {
        $node = dom_import_simplexml($source);
        $fragment = $node->ownerDocument->createDocumentFragment();
        $fragment->appendChild($node->cloneNode(TRUE));
        return $fragment;
      }
      throw new InvalidArgument('source', ['SimpleXMLElement', 'string']);
    }
  }
}