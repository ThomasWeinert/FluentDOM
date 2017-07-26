<?php
/**
 * FluentDOM\DOM\EntityReference extends PHPs DOMEntityReference class.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\DOM {

  class EntityReference extends \DOMEntityReference implements Node {

    use Node\StringCast;
    use Node\Xpath;
  }
}