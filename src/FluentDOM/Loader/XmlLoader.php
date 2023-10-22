<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
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
  class XmlLoader implements Loadable {

    use LoaderSupports\LibxmlSupports;

    public const LIBXML_OPTIONS = 'libxml';
    public const CONTENT_TYPES = ['xml', 'application/xml', 'text/xml'];

    /**
     * @see Loadable::load
     * @throws InvalidStringSource
     * @throws InvalidFileSource
     * @throws \Throwable
     */
    public function load(
      mixed $source, string $contentType, iterable $options = []
    ): ?LoaderResult {
      if ($this->supports($contentType)) {
        return new LoaderResult($this->loadXmlDocument($source, $options), $contentType);
      }
      return NULL;
    }

    /**
     * @see LoadableFragment::loadFragment
     * @throws \Throwable
     */
    public function loadFragment(
      mixed $source, string $contentType, iterable $options = []
    ): ?DocumentFragment {
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
