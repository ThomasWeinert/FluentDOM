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
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, $options = []) {
      if ($this->supports($contentType)) {
        return (new Libxml\Errors())->capture(
          function() use ($source, $contentType, $options) {
            $document = new Document();
            $document->preserveWhiteSpace = FALSE;
            $options = $this->getOptions($options);
            $loadOptions = (int)$options[self::LIBXML_OPTIONS];
            $options->isAllowed($sourceType = $options->getSourceType($source));
            switch ($sourceType) {
            case Options::IS_FILE :
              $document->load($source, $loadOptions);
              break;
            case Options::IS_STRING :
            default :
              $document->loadXML($source, $loadOptions);
              break;
            }
            return $document;
          }
        );
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
            $dom = new Document();
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXml($source);
            return $fragment;
          }
        );
      }
      return NULL;
    }

    /**
     * @param array|\Traversable|Options $options
     * @return Options
     */
    public function getOptions($options) {
      $result = new Options(
        $options,
        [
          'identifyStringSource' => function($source) {
            return $this->startsWith($source, '<');
          }
        ]
      );
      $result[self::LIBXML_OPTIONS] = (int)$result[self::LIBXML_OPTIONS];
      return $result;
    }
  }
}