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

    use Supports;

    const IS_FRAGMENT = 'fragment';
    const LIBXML_OPTIONS = 'libxml';

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
            $options = $this->getOptions($options);
            $loadOptions = $options[self::LIBXML_OPTIONS];
            if ($this->isFragment($contentType, $options)) {
              $this->loadFragmentIntoDom($document, $source, $loadOptions);
              $selection = $document->evaluate('/*');
            } else {
              $options->isAllowed($sourceType = $options->getSourceType($source));
              switch ($sourceType) {
              case Options::IS_FILE :
                $document->loadHTMLFile($source, $loadOptions);
                break;
              case Options::IS_STRING :
              default :
                $document->loadHTML($source, $loadOptions);
                break;
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
        $loadOptions = (int)$options[self::LIBXML_OPTIONS];
        return (new Libxml\Errors())->capture(
          function() use ($source, $loadOptions) {
            $document = new Document();
            $fragment = $document->createDocumentFragment();
            $document->loadHTML('<html-fragment>'.$source.'</html-fragment>', $loadOptions);
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