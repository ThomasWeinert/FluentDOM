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

    public const CONTENT_TYPES = ['php/pdo', 'pdo'];

    /**
     * @see Loadable::load
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document|Result|NULL
     * @throws \LogicException
     */
    public function load($source, string $contentType, $options = []): ?Result {
      if ($source instanceof \PDOStatement) {
        $document = new Document('1.0', 'UTF-8');
        $document->registerNamespace('json', self::XMLNS);
        $root = $document->appendElement('json:json');
        $source->setFetchMode(\PDO::FETCH_OBJ);
        foreach ($source as $row) {
          $child = $root->appendElement('_');
          $this->transferTo($child, $row, 2);
        }
        return new Result($document, 'text/xml');
      }
      return NULL;
    }

    /**
     * @see Loadable::loadFragment
     *
     * @param mixed $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return DocumentFragment|NULL
     * @throws InvalidFragmentLoader
     */
    public function loadFragment($source, string $contentType, $options = []): ?DocumentFragment {
      throw new InvalidFragmentLoader(self::class);
    }
  }
}
