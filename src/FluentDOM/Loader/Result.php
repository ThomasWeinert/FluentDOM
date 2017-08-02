<?php

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;

  class Result {

    /**
     * @var \DOMDocument
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