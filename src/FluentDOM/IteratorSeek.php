<?php
/**
 * A trait that provides seek for the Iterators.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */
namespace FluentDOM {

  /**
   * A trait that provides seek for the Iterators.
   */
  trait IteratorSeek {

    /**
     * Move iterator pointer to specified element
     *
     * @param integer $position
     * @throws \InvalidArgumentException
     */
    public function seek($position) {
      if ($this->_owner->count() > $position) {
        $this->_position = $position;
      } else {
        throw new \InvalidArgumentException(
          sprintf(
            'Unknown position %d, only %d items',
            $position, $this->_owner->count()
          )
        );
      }
    }
  }
}
