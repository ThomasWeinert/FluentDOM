<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\DOM {

  use DOMDocumentType;
  use FluentDOM\Exceptions\UnattachedNode;

  /**
   * Extend DOMImplementation to return FluentDOM\DOM classes
   */
  class Implementation extends \DOMImplementation {

    /**
     * @param string|null $namespaceURI
     * @param string|null $qualifiedName
     * @param DOMDocumentType|null $doctype
     * @return Document
     */
    public function createDocument(
      $namespaceURI = NULL, $qualifiedName = NULL, DOMDocumentType $doctype = NULL
    ) {
      $document = new Document();
      if ($doctype) {
        $document->appendChild($doctype);
      }
      if ($qualifiedName) {
        $document->appendChild($document->createElementNS($namespaceURI, $qualifiedName));
        $prefix = (string)strstr($qualifiedName, ':', TRUE);
        if ($prefix !== '' || !empty($namespaceURI)) {
          $document->registerNamespace($prefix, $namespaceURI);
        }
      }
      return $document;
    }

    /**
     * @param \DOMNode $node
     * @return \DOMDocument
     * @throws UnattachedNode
     */
    public static function getNodeDocument(\DOMNode $node): \DOMDocument {
      $document = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
      if (!$document) {
        throw new UnattachedNode();
      }
      return $document;
    }
  }
}
