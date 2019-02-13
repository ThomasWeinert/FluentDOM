<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;

  class Result {

    /**
     * @var Document
     */
    private $_document;
    /**
     * @var string
     */
    private $_contentType;
    /**
     * @var \DOMNode|\Traversable|array|NULL
     */
    private $_selection;

    /**
     * @param Document $document
     * @param string $contentType
     * @param \DOMNode|\Traversable|array|NULL $selection
     */
    public function __construct(Document $document, string $contentType, $selection = NULL) {
      $this->_document = $document;
      $this->_contentType = $contentType;
      $this->_selection = $selection;
    }

    /**
     * @return Document
     */
    public function getDocument(): Document {
      return $this->_document;
    }

    /**
     * @return string
     */
    public function getContentType(): string {
      return $this->_contentType;
    }

    /**
     * @return array|\DOMNode|NULL|\Traversable
     */
    public function getSelection() {
      return $this->_selection;
    }
  }
}
