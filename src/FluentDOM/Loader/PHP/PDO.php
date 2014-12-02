<?php
/**
 * Load a PDO result
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\PHP {

  use FluentDOM\Document;
  use FluentDOM\Loader\Json\JsonDOM;

  /**
   * Load a PDO result
   */
  class PDO extends JsonDOM {

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('php/pdo', 'pdo');
    }

    /**
     * @see Loadable::load
     * @param \PDOStatement $source
     * @param string $contentType
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if ($source instanceof \PDOStatement) {
        $dom = new Document('1.0', 'UTF-8');
        $dom->registerNamespace('json', self::XMLNS);
        $root = $dom->appendElement('json:json');
        $source->setFetchMode(\PDO::FETCH_OBJ);
        foreach ($source as $row) {
          $child = $root->appendElement('_');
          $this->transferTo($child, $row, 1);
        }
        return $dom;
      }
      return NULL;
    }
  }
}