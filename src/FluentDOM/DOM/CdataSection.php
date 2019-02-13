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
    implements Node\NonDocumentTypeChildNode {

    use Node\ChildNode\Implementation;
    use Node\NonDocumentTypeChildNode\Properties;
    use Node\StringCast;
    use Node\WholeText;
    use Node\Xpath;
  }
}
