<?php
/**
 * FluentDOM\CdataSection extends PHPs DOMCdataSection class.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\CdataSection extends PHPs DOMCdataSection class.
   *
   * @property Document $ownerDocument
   */
  class CdataSection
    extends \DOMCdataSection
    implements Node\ChildNode, Node\NonDocumentTypeChildNode {

    use Node\ChildNodeImplementation;
    use Node\NonDocumentTypeChildNodeImplementation
    use Node\StringCast;
    use Node\Xpath;
  }
}