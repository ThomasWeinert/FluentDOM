<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2018 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM {

  use FluentDOM\Utility\StringCastable;

  /**
   * @method string|float|bool|\DOMNodeList|Node[] __invoke()
   * @method string|float|bool|\DOMNodeList|Node[] evaluate(string $expression, \DOMNode $context = NULL)
   */
  interface Node extends StringCastable {

  }
}
