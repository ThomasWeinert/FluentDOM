<?php
/**
 * Load a PDO result
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\PHP {

  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidFragmentLoader;
  use FluentDOM\Loader\Json\JsonDOM;
  use FluentDOM\Loader\Result;

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
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     */
    public function load($source, $contentType, $options = []) {
      if ($source instanceof \PDOStatement) {
        $document = new Document('1.0', 'UTF-8');
        $document->registerNamespace('json', self::XMLNS);
        $root = $document->appendElement('json:json');
        $source->setFetchMode(\PDO::FETCH_OBJ);
        foreach ($source as $row) {
          $child = $root->appendElement('_');
          $this->transferTo($child, $row, 1);
        }
        return new Result($document, 'text/xml');
      }
      return NULL;
    }

    /**
     * @see Loadable::loadFragment
     *
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     */
    public function loadFragment($source, $contentType, $options = []) {
      throw new InvalidFragmentLoader(self::class);
    }
  }
}