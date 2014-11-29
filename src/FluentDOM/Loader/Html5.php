<?php
/**
 * Load a DOM document from a HTML5 string or file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\Loadable;

  use Masterminds\HTML5 as HTML5Support;

  /**
   * Load a DOM document from a HTML5 string or file
   */
  class Html5 implements Loadable {

    use Supports;

    /**
     * @return bool
     */
    private function isHtml5SupportInstalled() {
      return class_exists('Masterminds\\HTML5');
    }

    /**
     * @return string[]
     */
    public function getSupported() {
      return $this->isHtml5SupportInstalled() ? ['html5', 'text/html5'] : [];
    }

    /**
     * Load a HTML5 file and copy it into a FluentDOM\Document
     *
     * @codeCoverageIgnore
     *
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @return Document|NULL
     */
    public function load($source, $contentType) {
      if ($this->supports($contentType)) {
        $html5 = new HTML5Support();
        if ($this->startsWith($source, '<')) {
          $dom = $html5->loadHTML($source);
        } else {
          $dom = $html5->loadHTMLFile($source);
        }
        if (!$dom instanceof Document) {
          $fd = new Document();
          if ($dom->documentElement instanceof \DOMElement) {
            $fd->appendChild($fd->importNode($dom->documentElement, TRUE));
          }
          $dom = $fd;
        }
        $dom->registerNamespace(
          'html', 'http://www.w3.org/1999/xhtml'
        );
        return $dom;
      }
      return NULL;
    }
  }
}