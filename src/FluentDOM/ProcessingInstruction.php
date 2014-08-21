<?php
/**
 * FluentDOM\ProcessingInstruction extends PHPs DOMProcessingInstruction class.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * FluentDOM\ProcessingInstruction extends PHPs DOMProcessingInstruction class.
   *
   * @property Document $ownerDocument
   */
  class ProcessingInstruction
    extends \DOMProcessingInstruction  {

    use Node\StringCast;
    use Node\Xpath;
  }
}