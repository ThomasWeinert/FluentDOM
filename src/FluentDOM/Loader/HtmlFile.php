<?php
/**
 * Load a DOM document from a xml file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\Loadable;

  /**
   * Load a DOM document from a xml file
   */
  class HtmlFile extends HtmlString implements Loadable {

    /**
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @return Document|NULL
     */
    public function load($source, $contentType) {
      if ($this->supports($contentType) &&
          0 !== strpos($source, '<')) {
        return $this->createDocument(
          function(Document $dom) use ($source) {
            $dom->loadHTMLFile($source);
          }
        );
      }
      return NULL;
    }
  }
}