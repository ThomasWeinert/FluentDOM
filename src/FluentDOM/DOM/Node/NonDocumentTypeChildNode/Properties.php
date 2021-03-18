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

namespace FluentDOM\DOM\Node\NonDocumentTypeChildNode {

  trait Properties {

    use Implementation;

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling() !== NULL;
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling() !== NULL;
      }
      return isset($this->$name);
    }

    /**
     * @param string $name
     * @return \DOMNode|NULL
     */
    public function __get(string $name): ?\DOMNode {
      switch ($name) {
      case 'nextElementSibling' :
        return $this->getNextElementSibling();
      case 'previousElementSibling' :
        return $this->getPreviousElementSibling();
      }
      return $this->$name;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws \BadMethodCallException
     */
    public function __set(string $name, $value) {
      $this->blockReadOnlyProperties($name);
      $this->$name = $value;
    }

    /**
     * @param string $name
     * @throws \BadMethodCallException
     */
    public function __unset(string $name) {
      $this->blockReadOnlyProperties($name);
      unset($this->$name);
    }

    /**
     * @param string $name
     * @throws \BadMethodCallException
     */
    protected function blockReadOnlyProperties(string $name): void {
      switch ($name) {
      case 'nextElementSibling' :
      case 'previousElementSibling' :
        throw new \BadMethodCallException(
          \sprintf(
            'Cannot write property %s::$%s.',
            \get_class($this), $name
          )
        );
      }
    }
  }
}
