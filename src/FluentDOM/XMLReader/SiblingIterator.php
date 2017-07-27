<?php
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

    protected function move(XMLReader $reader, $name, $filter): bool {
      return ($this->key() < 0)
        ? $reader->read($name, NULL, $filter)
        : $reader->next($name, NULL, $filter);
    }
  }

}
