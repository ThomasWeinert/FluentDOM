<?php
/**
 * FluentDOM\DOM\CdataSection extends PHPs DOMCdataSection class.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\DOM {

  /**
   * FluentDOM\DOM\CdataSection extends PHPs DOMCdataSection class.
   *
   * @property-read Document $ownerDocument
   * @property-read Element $nextElementSibling
   * @property-read Element $previousElementSibling
   */
  class CdataSection
    extends \DOMCdataSection
    implements Node, Node\ChildNode, Node\NonDocumentTypeChildNode {

    use Node\ChildNode\Implementation;
    use Node\NonDocumentTypeChildNode\Properties;
    use Node\StringCast;
    use Node\WholeText;
    use Node\Xpath;
  }
}