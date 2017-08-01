<?php
/**
 * Load a DOM document from a xml string
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\DOM\ProcessingInstruction;
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
    public function getSupported(): array {
      return ['html', 'text/html', 'html-fragment', 'text/html-fragment'];
    }

    /**
     * @see Loadable::load
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     * @throws \FluentDOM\Exceptions\InvalidSource\TypeString
     * @throws \FluentDOM\Exceptions\InvalidSource\TypeFile
     */
    public function load($source, string $contentType, $options = []) {
      if ($this->supports($contentType)) {
        return (new Libxml\Errors())->capture(
          function() use ($source, $contentType, $options) {
            $selection = FALSE;
            $document = new Document();
            $settings = $this->getOptions($options);
            if ($this->isFragment($contentType, $settings)) {
              $this->loadFragmentIntoDom($document, $source, $settings);
              $selection = $document->evaluate('/node()');
            } else {
              $settings->isAllowed($sourceType = $settings->getSourceType($source));
              switch ($sourceType) {
              case Options::IS_FILE :
                $document->loadHTMLFile($source, $settings[Options::LIBXML_OPTIONS]);
                break;
              case Options::IS_STRING :
              default :
                $document->loadHTML(
                  $this->ensureEncodingPI($source, $settings[Options::ENCODING], $settings[Options::FORCE_ENCODING]),
                  $settings[Options::LIBXML_OPTIONS]
                );
              }
            }
            /** @var ProcessingInstruction $pi */
            if ($pi = $document->xpath()->firstOf('//processing-instruction()')) {
              $pi->remove();
            }
            return new Result($document, 'text/html', $selection);
          }
        );
      }
      return NULL;
    }

    private function ensureEncodingPI(string $source, string $encoding = NULL, bool $force = NULL): string {
      $hasXmlPi = preg_match('(<\\?xml\\s)', $source);
      if (!$force && ($charset = $this->getCharsetFromMetaTag($source))) {
        $encoding = (string)$charset;
      }
      $pi = '<?xml version="1.0" encoding="'.htmlspecialchars($encoding).'"?>';
      if (!$hasXmlPi) {
        return $pi.$source;
      }
      if ($force) {
        return preg_replace('(<\\?xml\\s[^?>]*?>)', $pi, $source, 1);
      }
      return $source;
    }

    /**
     * @param string $source
     * @return string|bool
     */
    private function getCharsetFromMetaTag(string $source) {
      $hasMetaTag = preg_match(
        '(<meta\\s+[^>]*charset=["\']\s*(?<charset>[^\\s\'">]+)\s*["\'])i',
        $source,
        $match
      );
      if ($hasMetaTag) {
        return $match['charset'];
      }
      $hasMetaTag = preg_match(
        '(<meta\\s+[^>]*http-equiv=["\']content-type["\'][^>]*>)i',
        $source,
        $match
      );
      if ($hasMetaTag) {
        preg_match(
          '(content=["\']\s*[^#\']+;\s*charset\s*=\s*(?<encoding>[^\S\'">]+))',
          $match[0],
          $match
        );
        return $match['encoding'] ?? FALSE;
      }
      return FALSE;
    }

    /**
     * @see LoadableFragment::loadFragment
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment($source, string $contentType, $options = []) {
      if ($this->supports($contentType)) {
        $options = $this->getOptions($options);
        return (new Libxml\Errors())->capture(
          function() use ($source, $options) {
            $document = new Document();
            $fragment = $document->createDocumentFragment();
            $document->loadHTML(
              $this->ensureEncodingPI(
                '<html-fragment>'.$source.'</html-fragment>',
                $options[Options::ENCODING],
                $options[Options::FORCE_ENCODING]
              ),
              $options[Options::LIBXML_OPTIONS]
            );
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

    private function isFragment(string $contentType, $options) {
      return (
        $contentType === 'html-fragment' ||
        $contentType === 'text/html-fragment' ||
        $options[self::IS_FRAGMENT]
      );
    }

    private function loadFragmentIntoDom(\DOMDocument $document, string $source, $settings) {
      $htmlDom = new Document();
      $htmlDom->loadHTML(
        $this->ensureEncodingPI(
          '<html-fragment>'.$source.'</html-fragment>',
          $settings[Options::ENCODING],
          $settings[Options::FORCE_ENCODING]
        ),
        $settings[Options::LIBXML_OPTIONS]
      );
      $nodes = $htmlDom->evaluate('//html-fragment[1]/node()');
      foreach ($nodes as $node) {
        /** @var \DOMNode $node */
        if ($importedNode = $document->importNode($node, TRUE)) {
          $document->appendChild($importedNode);
        }
      }
    }
  }
}