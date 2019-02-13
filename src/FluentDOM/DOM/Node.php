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

  use FluentDOM\Utility\StringCastable;

  /**
   *
   * @property-read Document|NULL $ownerDocument
   * @method string|float|bool|\DOMNodeList|Node[] __invoke()
   * @method string|float|bool|\DOMNodeList|Node[] evaluate(string $expression, Node $context = NULL)
   */
  interface Node extends StringCastable {

  }
}
