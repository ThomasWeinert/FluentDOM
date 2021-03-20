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

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidSource\TypeFile as InvalidFileSource;
  use FluentDOM\Exceptions\InvalidSource\TypeString as InvalidStringSource;
  use FluentDOM\Loadable;

  /**
   * Load a DOM document from a xml file or string
   */
  class Xml implements Loadable {

    use Supports\Libxml;

    public const LIBXML_OPTIONS = 'libxml';
    public const CONTENT_TYPES = ['xml', 'application/xml', 'text/xml'];

    /**
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     * @throws InvalidStringSource
     * @throws InvalidFileSource
     * @throws \Throwable
     */
    public function load($source, string $contentType, $options = []): ?Result {
      if ($this->supports($contentType)) {
        return new Result($this->loadXmlDocument($source, $options), $contentType);
      }
      return NULL;
    }

    /**
     * @see LoadableFragment::loadFragment
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     * @throws \Throwable
     */
    public function loadFragment($source, string $contentType, $options = []): ?DocumentFragment {
      if ($this->supports($contentType)) {
        return (new Libxml\Errors())->capture(
          static function() use ($source) {
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
