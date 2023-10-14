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

namespace FluentDOM\Loader\Json {

  use FluentDOM\DOM\Document;
  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Result;
  use FluentDOM\Loader\Supports;

  /**
   * Load a DOM document from a string or file that was the result of a
   * SimpleXMLElement encoded as JSON.
   */
  class SimpleXML implements Loadable {

    use Supports\Json;
    public const CONTENT_TYPES = ['text/simplexml', 'text/simplexml+json', 'application/simplexml+json'];

    private const XMLNS = 'urn:carica-json-dom.2013';

    /**
     * Load the json string into an DOMDocument
     *
     * @throws InvalidSource|\DOMException
     */
    public function load($source, string $contentType, iterable $options = []): ?Result {
      if (FALSE !== ($json = $this->getJson($source, $contentType, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $document->appendChild(
          $root = $document->createElementNS(self::XMLNS, 'json:json')
        );
        $this->transferTo($root, $json);
        return new Result($document, $contentType);
      }
      return NULL;
    }

    protected function transferTo(\DOMNode $target, mixed $json): void {
      /** @var Document $document */
      $document = $target->ownerDocument ?: $target;
      if ($json instanceof \stdClass) {
        foreach ($json as $name => $data) {
          if ($name === '@attributes') {
            if ($data instanceof \stdClass && $target instanceof \DOMElement) {
              foreach ($data as $attributeName => $attributeValue) {
                $target->setAttribute($attributeName, $this->getValueAsString($attributeValue));
              }
            }
          } else {
            $this->transferChildTo($target, $name, $data);
          }
        }
      } elseif (is_scalar($json)) {
        $target->appendChild(
          $document->createTextNode($this->getValueAsString($json))
        );
      }
    }

    /**
     * @throws \DOMException
     */
    protected function transferChildTo(\DOMNode $node, string $name, mixed $data): array {
      /** @var Document $document */
      $document = $node->ownerDocument ?: $node;
      if (!\is_array($data)) {
        $data = [$data];
      }
      foreach ($data as $dataChild) {
        $child = $node->appendChild($document->createElement($name));
        $this->transferTo($child, $dataChild);
      }
      return $data;
    }
  }
}
