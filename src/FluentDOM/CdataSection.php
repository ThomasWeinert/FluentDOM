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
   * @property-read Document $ownerDocument
   * @property-read Element $nextElementSibling
   * @property-read Element $previousElementSibling
   */
  class CdataSection
    extends \DOMCdataSection
    implements Node\ChildNode, Node\NonDocumentTypeChildNode {

    use Node\ChildNode\Implementation;
    use Node\NonDocumentTypeChildNode\Properties;
    use Node\StringCast;
    use Node\Xpath;
  }
}