<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Transformer {

  require_once __DIR__ . '/../TestCase.php';

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\TestCase;

  class NamespacesTest extends TestCase {

    /**
     * @covers \FluentDOM\Transformer\Namespaces
     */
    public function testConstructorWithDocument(): void {
      $document = new Document();
      $document->loadXML(self::XML);

      $transformer = new Namespaces_TestProxy($document);
      $this->assertXmlStringEqualsXmlString(
        self::XML,
        (string)$transformer
      );
    }

    /**
     * @covers \FluentDOM\Transformer\Namespaces
     */
    public function testConstructorWithNode(): void {
      $document = new Document();
      $document->loadXML(self::XML);

      $transformer = new Namespaces_TestProxy($document->documentElement);
      $this->assertXmlStringEqualsXmlString(
        self::XML,
        (string)$transformer
      );
    }

    /**
     * @covers \FluentDOM\Transformer\Namespaces
     */
    public function testIsTraversable(): void {
      $document = new Document();
      $document->loadXML(self::XML);

      $transformer = new Namespaces_TestProxy($document);
      /** @var Element $node */
      $node = iterator_to_array($transformer, FALSE)[0];
      $this->assertXmlStringEqualsXmlString(
        self::XML,
        $node->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Transformer\Namespaces
     */
    public function testIsAppendable(): void {
      $source = new Document();
      $source->loadXML(self::XML);

      $target = new Document();
      $target->appendChild($target->createElement('test'));

      $transformer = new Namespaces_TestProxy($source);
      $target->documentElement->append($transformer);

      $this->assertXmlStringEqualsXmlString(
        self::XML,
        $target->documentElement->firstElementChild->saveXml()
      );
    }
  }

  class Namespaces_TestProxy extends Namespaces {

    /**
     * @throws UnattachedNode
     */
    protected function addNode(\DOMNode $target, \DOMNode $source): void {
      /** @var Document|Element $target */
      $target->append($source);
    }
  }
}
