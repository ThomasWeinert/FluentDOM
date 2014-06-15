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
     * Load the source as an HTML string. The first character
     * must be an "<".
     *
     * @param string $source
     * @param string $contentType
     * @return Document|NULL
     */
    public function load($source, $contentType) {
      if ($this->supports($contentType)) {
        return $this->createDocument(
          function(Document $dom) use ($source) {
            $dom->loadHTML($source);
          },
          function () use ($source) {
            return 0 === strpos($source, '<');
          }
        );
      }
      return NULL;
    }


    /**
     * Create the dom and apply the $load callback on it, ignore libxml errors.
     * If validate is provided it is used to validate the source before calling
     * the load callback.
     *
     * @param callable $load
     * @param callable $validate
     * @return Document|NULL
     */
    protected function createDocument(callable $load, callable $validate = NULL) {
      if (NULL === $validate || $validate()) {
        $dom = new Document();
        $errorSetting = libxml_use_internal_errors(TRUE);
        $load($dom);
        libxml_clear_errors();
        libxml_use_internal_errors($errorSetting);
        return $dom;
      }
      return NULL;
    }
  }
}