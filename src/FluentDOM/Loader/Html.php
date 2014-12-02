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

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('html', 'text/html');
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
        if ($this->startsWith($source, '<')) {
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
  }
}