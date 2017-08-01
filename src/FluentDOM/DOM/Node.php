<?php
/**
 * FluentDOM\DOM\Node is an interface implemented by all the extended DOM node classes
 * in FluentDOM
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\DOM {

  use FluentDOM\Utility\StringCastable;

  /**
   * @method string|float|bool|\DOMNodeList|Node[] __invoke()
   * @method string|float|bool|\DOMNodeList|Node[] evaluate($expression, \DOMNode $context = NULL)
   */
  interface Node extends StringCastable {

  }
}