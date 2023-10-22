<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader\PHP {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidFragmentLoader;
  use FluentDOM\Loader\Json\JsonDOMLoader;
  use FluentDOM\Loader\LoaderResult;

  /**
   * Load a PDOLoader result
   */
  class PDOLoader extends JsonDOMLoader {

    public const CONTENT_TYPES = ['php/pdo', 'pdo'];

    /**
     * @throws \LogicException|\DOMException
     * @see Loadable::load
     */
    public function load(mixed $source, string $contentType, iterable $options = []): ?LoaderResult {
      if ($source instanceof \PDOStatement) {
        $document = new Document('1.0', 'UTF-8');
        $document->registerNamespace('json', self::XMLNS);
        $root = $document->appendElement('json:json');
        $source->setFetchMode(\PDO::FETCH_OBJ);
        foreach ($source as $row) {
          $child = $root->appendElement('_');
          $this->transferTo($child, $row, 2);
        }
        return new LoaderResult($document, 'text/xml');
      }
      return NULL;
    }

    /**
     * @see Loadable::loadFragment
     *
     * @throws InvalidFragmentLoader
     */
    public function loadFragment(mixed $source, string $contentType, iterable $options = []): ?DocumentFragment {
      throw new InvalidFragmentLoader(self::class);
    }
  }
}
