<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM\Node\ParentNode {

  trait Properties {

    use Implementation;

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name) {
      switch ($name) {
      case 'firstElementChild' :
        return $this->getFirstElementChild() !== NULL;
      case 'lastElementChild' :
        return $this->getLastElementChild() !== NULL;
      case 'childElementCount' :
        return TRUE;
      }
      return isset($this->$name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
      switch ($name) {
      case 'firstElementChild' :
        return $this->getFirstElementChild();
      case 'lastElementChild' :
        return $this->getLastElementChild();
      case 'childElementCount' :
        return (int)$this->evaluate('count(*)');
      }
      return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) {
      $this->blockReadOnlyProperties($name);
      $this->$name = $value;
    }

    /**
     * @param string $name
     */
    public function __unset(string $name) {
      $this->blockReadOnlyProperties($name);
      unset($this->$name);
    }

    /**
     * @param string $name
     * @throws \BadMethodCallException
     */
    protected function blockReadOnlyProperties(string $name) {
      switch ($name) {
      case 'firstElementChild' :
      case 'lastElementChild' :
      case 'childElementCount' :
        throw new \BadMethodCallException(
          \sprintf(
            'Can not write readonly property %s::$%s.',
            \get_class($this), $name
          )
        );
      }
    }
  }

}
