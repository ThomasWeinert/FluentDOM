<?php
/**
 * Create an object from a string that contains a valid Qualified XML name.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM {

  /**
   * Create an object from a string that contains a valid Qualified XML name.
   *
   * @property-read string $name
   * @property-read string $localName
   * @property-read string $prefix
   */
  class QualifiedName {

    public static $cacheLimit = 100;
    private static $_cache = [];

    private $_prefix = '';
    private $_localName = '';

    public function __construct($name) {
      $this->setQName($name);
    }

    /**
     * Validate string as qualified node name
     *
     * @param string $name
     * @throws \UnexpectedValueException
     */
    private function setQName($name) {
      if (empty($name)) {
        throw new \UnexpectedValueException('Invalid QName: QName is empty.');
      } elseif (isset(self::$_cache[$name])) {
        $this->_prefix = self::$_cache[$name][0];
        $this->_localName = self::$_cache[$name][1];
        return;
      } elseif (FALSE !== ($position = strpos($name, ':'))) {
        list($prefix, $localName) = explode(':', $name, 2);
        $this->isNCName($prefix, 0, $name);
        $this->isNCName($name, $position + 1);
        $this->_prefix = $prefix;
        $this->_localName = $localName;
      } else {
        $this->isNCName($name);
        $this->_localName = $name;
      }
      if (count(self::$_cache) > self::$cacheLimit) {
        array_splice(self::$_cache, 0, self::$cacheLimit / 2);
      }
      self::$_cache[$name] = [$this->_prefix, $this->_localName];
    }

    /**
     * Validate string as qualified node name part (namespace or local name)
     *
     * @param string $name full QName
     * @param integer $offset Offset of NCName part in QName
     * @param string $fullName full name used in error message
     * @throws \UnexpectedValueException
     * @return boolean
     */
    private function isNCName($name, $offset = 0, $fullName = NULL) {
      $nameStartChar =
        'A-Z_a-z'.
        '\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}'.
        '\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}'.
        '\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}'.
        '\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}';
      $nameChar =
        $nameStartChar.
        '\\.\\d\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}';
      if ($offset > 0) {
        $namePart = substr($name, $offset);
      } else {
        $namePart = $name;
        $name = $fullName ?: $namePart;
      }
      if (empty($namePart)) {
        throw new \UnexpectedValueException(
          'Invalid QName "'.$name.'": Missing QName part.'
        );
      } elseif (preg_match('([^'.$nameChar.'-])u', $namePart, $match, PREG_OFFSET_CAPTURE)) {
        //invalid bytes and whitespaces
        $position = (int)$match[0][1];
        throw new \UnexpectedValueException(
          'Invalid QName "'.$name.'": Invalid character at index '.($offset + $position).'.'
        );
      } elseif (preg_match('(^[^'.$nameStartChar.'])u', $namePart)) {
        //first char is a little more limited
        throw new \UnexpectedValueException(
          'Invalid QName "'.$name.'": Invalid character at index '.$offset.'.'
        );
      }
      return TRUE;
    }

    /**
     * Allow to convert the qualified name object to a string.
     *
     * @return string
     */
    public function __toString() {
      return $this->name;
    }

    /**
     * Define dynamic properties, return false for all other
     *
     * @param $property
     * @return bool
     */
    public function __isset($property) {
      switch ($property) {
      case 'name' :
      case 'localName' :
      case 'prefix' :
        return TRUE;
      }
      return FALSE;
    }

    /**
     * Read dynamic property, throw exception for invalid properties.
     *
     * @param $property
     * @return string
     * @throws \LogicException
     */
    public function __get($property) {
      switch ($property) {
      case 'name' :
        return empty($this->_prefix) ? $this->_localName : $this->_prefix.':'.$this->_localName;
      case 'localName' :
        return $this->_localName;
      case 'prefix' :
        return $this->_prefix;
      }
      throw new \LogicException(
        sprintf('Invalid property %s::$%s', get_class($this), $property)
      );
    }

    /**
     * Block changes
     *
     * @param $property
     * @param $value
     * @throws \LogicException
     */
    public function __set($property, $value) {
      throw new \LogicException(
        sprintf('%s is immutable.', get_class($this))
      );
    }

    /**
     * Block changes
     *
     * @param $property
     * @throws \LogicException
     */
    public function __unset($property) {
      throw new \LogicException(
        sprintf('%s is immutable.', get_class($this))
      );
    }

    /**
     * Split an qualified name into its two parts.
     *
     * @param string $name
     * @return array
     */
    public static function split($name) {
      if (FALSE !== ($position = strpos($name, ':'))) {
        $prefix = substr($name, 0, $position);
        $localName = substr($name, $position + 1);
      } else {
        $prefix = FALSE;
        $localName = $name;
      }
      return array(
        $prefix,
        $localName
      );
    }

    /**
     * Validate a string to be an valid QName
     *
     * @param string $name
     * @return bool
     */
    public static function validate($name) {
      try {
        new QualifiedName($name);
      } catch (\UnexpectedValueException $e) {
        return FALSE;
      }
      return TRUE;
    }


    /**
     * Removes all characters from a string that are not allowed in a xml NCName. An NCName is the
     * tag name of an xml element without a prefix.
     *
     * If the result of that removal is an empty string, the default value is returned.
     *
     * @param string $string
     * @param string $default
     * @return string
     */
    public static function normalizeString($string, $default = '_') {
      $nameStartChar =
        'A-Z_a-z'.
        '\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}'.
        '\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}'.
        '\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}'.
        '\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}';
      $nameAdditionalChar =
        $nameStartChar.
        '\\.\\d\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}';
      $result = preg_replace(
        array(
          '([^'.$nameAdditionalChar.'-]+)u',
          '(^[^'.$nameStartChar.']+)u',
        ),
        '',
        $string
      );
      return (empty($result)) ? $default : $result;
    }
  }
}