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

namespace FluentDOM\DOM {

  use DOMDocumentType;
  use FluentDOM\Exceptions\UnattachedNode;

  /**
   * Extend DOMImplementation to return FluentDOM\DOM classes
   */
  class Implementation extends \DOMImplementation {

    public function createDocument(
      string $namespace = NULL,
      string $qualifiedName = NULL,
      DOMDocumentType $doctype = NULL
    ): Document {
      $document = new Document();
      if ($doctype) {
        $document->appendChild($doctype);
      }
      if ($qualifiedName) {
        $document->appendChild($document->createElementNS($namespace, $qualifiedName));
        $prefix = (string)strstr($qualifiedName, ':', TRUE);
        if ($prefix !== '' || !empty($namespace)) {
          $document->registerNamespace($prefix, $namespace);
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
