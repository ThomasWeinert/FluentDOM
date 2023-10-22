<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
/**
 * @noinspection PhpComposerExtensionStubsInspection
 */
declare(strict_types=1);

namespace FluentDOM\Loader\PHP {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\LoaderResult;
  use FluentDOM\Loader\LoaderSupports;
  use FluentDOM\Loader\XmlLoader;

  /**
   * Load a DOM document from a SimpleXMLLoader element
   */
  class SimpleXmlLoader implements Loadable {

    use LoaderSupports;
    public const CONTENT_TYPES = ['simplexml', 'php/simplexml'];

    /**
     * @var XmlLoader|NULL
     */
    private ?XmlLoader $_xmlLoader = NULL;

    /**
     * @see Loadable::load
     */
    public function load(mixed $source, string $contentType, iterable $options = []): ?LoaderResult {
      if ($source instanceof \SimpleXMLElement) {
        $document = new Document();
        $document->appendChild($document->importNode(\dom_import_simplexml($source), TRUE));
        return new LoaderResult($document, 'text/xml');
      }
      return NULL;
    }

    /**
     * @see Loadable::loadFragment
     *
     * @throws \Throwable
     */
    public function loadFragment(mixed $source, string $contentType, iterable $options = []): ?DocumentFragment {
      if (!$this->supports($contentType)) {
        return NULL;
      }
      if (\is_string($source)) {
        $this->_xmlLoader = $this->_xmlLoader ?: new XmlLoader();
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
