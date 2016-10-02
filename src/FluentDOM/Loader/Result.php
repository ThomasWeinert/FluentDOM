<?php

namespace FluentDOM\Loader {

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
    private $_selection = NULL;

    /**
     * @param \DOMDocument $document
     * @param string $contentType
     * @param \DOMNode|\Traversable|array|NULL $selection
     */
    public function __construct(\DOMDocument $document, $contentType, $selection = NULL) {
      $this->_document = $document;
      $this->_contentType = (string)$contentType;
      $this->_selection = $selection;
    }

    public function getDocument() {
      return $this->_document;
    }

    public function getContentType() {
      return $this->_contentType;
    }

    public function getSelection() {
      return $this->_selection;
    }
  }
}