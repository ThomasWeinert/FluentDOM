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
  class HtmlFile implements Loadable {

    use Supports;

    /**
     * @var array
     */
    protected $_supportedTypes = array(
      'html', 'text/html'
    );

    /**
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @return Document|NULL
     */
    public function load($source, $contentType) {
      if ($this->supports($contentType) &&
          0 !== strpos($source, '<')) {
        $dom = new Document();
        $errorSetting = libxml_use_internal_errors(TRUE);
        libxml_clear_errors();
        $dom->loadHTMLFile($source);
        libxml_clear_errors();
        libxml_use_internal_errors($errorSetting);
        return $dom;
      }
      return NULL;
    }

  }
}