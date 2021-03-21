<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\XMLReader {

  use FluentDOM\XMLReader;

  /**
   * Class SiblingIterator
   *
   * It will use XMLReader::read() to find the first node and XMLReader::next() to iterate its siblings after that.
   *
   * It uses expand to return each found node as DOM node.
   */
  class SiblingIterator extends Iterator {

    protected function move(
      XMLReader $reader, string $name = NULL, callable $filter = NULL
    ): bool {
      return ($this->key() < 0)
        ? $reader->read($name, NULL, $filter)
        : $reader->next($name, NULL, $filter);
    }
  }

}
