<?php
/**
 * Load a PDO result
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Loader\PHP {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidFragmentLoader;
  use FluentDOM\Loader\Json\JsonDOM;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Result;

  /**
   * Load a PDO result
   */
  class PDO extends JsonDOM {

    /**
     * @return string[]
     */
    public function getSupported(): array {
      return ['php/pdo', 'pdo'];
    }

    /**
     * @see Loadable::load
     * @param \PDOStatement $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     * @throws \LogicException
     */
    public function load($source, string $contentType, $options = []) {
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
     * @throws \FluentDOM\Exceptions\InvalidFragmentLoader
     */
    public function loadFragment($source, string $contentType, $options = []) {
      throw new InvalidFragmentLoader(self::class);
    }
  }
}