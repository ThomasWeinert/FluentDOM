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

namespace FluentDOM\Loader {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\DOM\Element;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Json\JsonDOM;
  use FluentDOM\Utility\Constraints;
  use FluentDOM\Utility\QualifiedName;

  /**
   * Load IBMs JSONx format.
   */
  class JSONx implements Loadable {

    use Supports\Libxml;
    public const CONTENT_TYPES = ['jsonx', 'application/xml+jsonx'];

    /** @noinspection HttpUrlsUsage */
    private const XMLNS_JSONX = 'http://www.ibm.com/xmlns/prod/2009/jsonx';
    private const XMLNS_JSONDOM = JsonDOM::XMLNS;
    private const DEFAULT_QNAME = '_';

    /**
     * @see Loadable::load
     * @throws \Throwable
     */
    public function load(
      mixed $source, string $contentType, iterable $options = []
    ): ?Result {
      if (NULL !== $source && $this->supports($contentType)) {
        $document = $this->loadXmlDocument($source, $options);
        $target = new Document();
        $target->registerNamespace('json', self::XMLNS_JSONDOM);
        if (isset($document->documentElement)) {
          $this->transferNode($document->documentElement, $target);
        }
        return new Result($target, $contentType);
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
     * @throws \Throwable
     */
    public function loadFragment($source, string $contentType, $options = []): ?DocumentFragment {
      if (NULL !== $source && $this->supports($contentType)) {
        $document = new Document();
        $document->preserveWhiteSpace = FALSE;
        $document->registerNamespace('jx', self::XMLNS_JSONX);
        $sourceFragment = $document->createDocumentFragment();
        $sourceFragment->appendXml($source);
        $target = new Document();
        $target->registerNamespace('json', self::XMLNS_JSONDOM);
        $targetFragment = $target->createDocumentFragment();
        foreach ($sourceFragment->childNodes as $node) {
          $this->transferNode($node, $targetFragment);
        }
        return $targetFragment;
      }
      return NULL;
    }

    /**
     * @throws \Throwable
     */
    private function transferNode(
      Element $node, Document|Element|DocumentFragment $target
    ): void {
      if ($node->namespaceURI === self::XMLNS_JSONX) {
        if ($target instanceof Document) {
          $normalizedName = $name = 'json:json';
        } else {
          $name = $node->getAttribute('name');
          $normalizedName = QualifiedName::normalizeString($name, self::DEFAULT_QNAME);
        }
        $type = $node->localName;
        $newNode = $target->appendElement($normalizedName);
        if ($name !== $normalizedName && $name !== '') {
          $newNode->setAttribute('json:name', $name);
        }
        $implicit = false;
        if ($type === 'object') {
          $implicit = $this->transferChildElements($node, $newNode);
        } elseif ($type === 'array') {
          $this->transferChildElements($node, $newNode);
        } elseif ($type === 'string') {
          $newNode->append((string)$node);
          return;
        } else {
          $newNode->append((string)$node);
        }
        if (!$implicit) {
          $newNode->setAttribute('json:type', $type);
        }
      }
    }

    private function transferChildElements(Element $node, Element $target): bool {
      if ($node('count(*) > 0')) {
        foreach ($node('*') as $childNode) {
          /** @var Element $childNode */
          $this->transferNode($childNode, $target);
        }
        return true;
      }
      return false;
    }
  }
}
