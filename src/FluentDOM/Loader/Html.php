<?php
/**
 * Load a DOM document from a xml string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Loadable;

  /**
   * Load a DOM document from a xml string
   */
  class Html implements Loadable {

    use Supports\Libxml;

    const IS_FRAGMENT = 'is_fragment';

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('html', 'text/html', 'html-fragment', 'text/html-fragment');
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
            $selection = false;
            $document = new Document();
            $settings = $this->getOptions($options);
            if ($this->isFragment($contentType, $settings)) {
              $this->loadFragmentIntoDom($document, $source, $settings[Options::LIBXML_OPTIONS]);
              $selection = $document->evaluate('/*');
            } else {
              $settings->isAllowed($sourceType = $settings->getSourceType($source));
              switch ($sourceType) {
              case Options::IS_FILE :
                $document->loadHTMLFile($source, $settings[Options::LIBXML_OPTIONS]);
                break;
              case Options::IS_STRING :
              default :
                $document->loadHTML($source, $settings[Options::LIBXML_OPTIONS]);
              }
            }
            return new Result($document, 'text/html', $selection);
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
        $options = $this->getOptions($options);
        return (new Libxml\Errors())->capture(
          function() use ($source, $options) {
            $document = new Document();
            $fragment = $document->createDocumentFragment();
            $document->loadHTML('<html-fragment>'.$source.'</html-fragment>', $options[Options::LIBXML_OPTIONS]);
            $nodes = $document->evaluate('//html-fragment[1]/node()');
            foreach ($nodes as $node) {
              $fragment->append($node);
            }
            return $fragment;
          }
        );
      }
      return NULL;
    }

    private function isFragment($contentType, $options) {
      return (
        $contentType == 'html-fragment' ||
        $contentType == 'text/html-fragment' ||
        $options[self::IS_FRAGMENT]
      );
    }

    private function loadFragmentIntoDom(\DOMDocument $document, $source, $loadOptions) {
      $htmlDom = new Document();
      $htmlDom->loadHTML('<html-fragment>'.$source.'</html-fragment>', $loadOptions);
      $nodes = $htmlDom->evaluate('//html-fragment[1]/node()');
      foreach ($nodes as $node) {
        if ($importedNode = $document->importNode($node, TRUE)) {
          $document->appendChild($importedNode);
        }
      }
    }
  }
}