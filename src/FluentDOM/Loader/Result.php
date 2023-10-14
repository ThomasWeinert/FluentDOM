<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;

  class Result {

    private Document $_document;
    private string $_contentType;
    private NULL|\DOMNode|iterable $_selection;

    public function __construct(
      Document $document, string $contentType, \DOMNode|iterable $selection = NULL
    ) {
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

    public function getContentType(): string {
      return $this->_contentType;
    }

    /** @noinspection PhpUnused */
    public function getSelection(): NULL|\DOMNode|iterable {
      return $this->_selection;
    }
  }
}
