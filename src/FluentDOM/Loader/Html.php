<?php
/**
 * Load a DOM document from a xml string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\Loadable;

  /**
   * Load a DOM document from a xml string
   */
  class Html implements Loadable {

    use Supports;

    const IS_FRAGMENT = 'fragment';

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
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if ($this->supports($contentType)) {
        $dom = new Document();
        $errorSetting = libxml_use_internal_errors(TRUE);
        if ($this->isFragment($contentType, $options)) {
          $this->loadFragmentIntoDom($dom, $source);
        } else if ($this->startsWith($source, '<')) {
          $dom->loadHTML($source);
        } else {
          $dom->loadHTMLFile($source);
        }
        libxml_clear_errors();
        libxml_use_internal_errors($errorSetting);
        return $dom;
      }
      return NULL;
    }

    private function isFragment($contentType, $options) {
      return (
        $contentType == 'html-fragment' ||
        $contentType == 'text/html-fragment' ||
        (isset($options[self::IS_FRAGMENT]) && $options[self::IS_FRAGMENT])
      );
    }

    private function loadFragmentIntoDom($dom, $source) {
      $htmlDom = new Document();
      $htmlDom->loadHtml('<html-fragment>'.$source.'</html-fragment>');
      $result = array();
      $nodes = $htmlDom->evaluate('//html-fragment[1]/node()');
      foreach ($nodes as $node) {
        $dom->appendChild($dom->importNode($node, TRUE));
      }
    }
  }
}