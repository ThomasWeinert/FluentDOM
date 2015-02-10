<?php
/**
 * A functor that allows to compare two css property names.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */
namespace FluentDOM\Query\Css {

  /**
   * A functor that allows to compare two css property names.
   */
  class PropertyCompare {

    public function __invoke() {
      return call_user_func_array(array($this, 'compare'), func_get_args());
    }

    /**
     * Compare to css property names by name, browser-prefix and level.
     *
     * @param string $propertyNameOne
     * @param string $propertyNameTwo
     * @return integer
     */
    public function compare($propertyNameOne, $propertyNameTwo) {
      $propertyOne = $this->_decodeName($propertyNameOne);
      $propertyTwo = $this->_decodeName($propertyNameTwo);
      $propertyOneLevels = count($propertyOne);
      $propertyTwoLevels = count($propertyTwo);
      $maxLevels = ($propertyOneLevels > $propertyTwoLevels)
        ? $propertyOneLevels : $propertyTwoLevels;
      for ($i = 0; $i < $maxLevels; ++$i) {
        if (
          isset($propertyOne[$i]) &&
          isset($propertyTwo[$i])
        ) {
          $compare = strnatcasecmp(
            $propertyOne[$i],
            $propertyTwo[$i]
          );
          if ($compare != 0) {
            return $compare;
          }
        } else {
          break;
        }
      }
      if ($propertyOneLevels > $propertyTwoLevels) {
        return 1;
      } elseif ($propertyOneLevels < $propertyTwoLevels) {
        return -1;
      } else {
        return 0;
      }
    }

    /**
     * Decodes the css property name into an comparable array.
     *
     * @param string $propertyName
     * @return array
     */
    private function _decodeName($propertyName) {
      if (substr($propertyName, 0, 1) === '-') {
        $pos = strpos($propertyName, '-', 1);
        $items = explode('-', substr($propertyName, $pos + 1));
        $items[] = substr($propertyName, 1, $pos);
        return $items;
      } else {
        $items = explode('-', $propertyName);
        return $items;
      }
    }
  }
}
