<?php
/**
 * Load a DOM document from a SimpleXML element
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\PHP {

  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidFragmentLoader;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Result;
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
      return array('simplexml', 'php/simplexml');
    }

    /**
     * @see Loadable::load
     * @param \SimpleXMLElement $source
     * @param string $contentType
     * @param array $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, array $options = []) {
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
     * @param array $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment($source, $contentType, array $options = []) {
      // TODO: Implement loadFragment() method.
      throw new InvalidFragmentLoader(self::class);
    }
  }
}