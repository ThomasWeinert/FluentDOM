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
  class HtmlString implements Loadable {

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
          0 === strpos($source, '<')) {
        return $this->createDocument(
          function(Document $dom) use ($source) {
            $dom->loadHTML($source);
          }
        );
      }
      return NULL;
    }


    /**
     * Create the dom and apply the callback on it, ignore libxml errors
     *
     * @param callable $source
     * @return Document
     */
    protected function createDocument(callable $callback) {
      $dom = new Document();
      $errorSetting = libxml_use_internal_errors(TRUE);
      $callback($dom);
      libxml_clear_errors();
      libxml_use_internal_errors($errorSetting);
      return $dom;
    }
  }
}