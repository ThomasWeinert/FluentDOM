<?php

namespace FluentDOM\Nodes {

  use FluentDOM\DOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class CompareTest extends TestCase {

    /**
     * @covers \FluentDOM\Nodes\Compare
     */
    public function testCompareDocumentElementWithChildNode() {
      $document = new Document();
      $document->loadXML('<main><child/></main>');
      $compare = new Compare($document->xpath());
      $this->assertEquals(
        -1,
        $compare(
          $document->xpath()->firstOf('/*'),
          $document->xpath()->firstOf('/*/*')
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Compare
     */
    public function testCompareChildNodeWithDocumentElement() {
      $document = new Document();
      $document->loadXML('<main><child/></main>');
      $compare = new Compare($document->xpath());
      $this->assertEquals(
        1,
        $compare(
          $document->xpath()->firstOf('/*/*'),
          $document->xpath()->firstOf('/*')
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Compare
     */
    public function testCompareDocumentElementWithItself() {
      $document = new Document();
      $document->loadXML('<main><child/></main>');
      $compare = new Compare($document->xpath());
      $this->assertEquals(
        0,
        $compare(
          $document->xpath()->firstOf('/*'),
          $document->xpath()->firstOf('/*')
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Compare
     */
    public function testCompareParentNodeWithChildNode() {
      $document = new Document();
      $document->loadXML('<main><parent><child/></parent></main>');
      $compare = new Compare($document->xpath());
      $this->assertEquals(
        -1,
        $compare(
          $document->xpath()->firstOf('/*/parent'),
          $document->xpath()->firstOf('/*/*/child')
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Compare
     */
    public function testCompareChildNodeWithParentNode() {
      $document = new Document();
      $document->loadXML('<main><parent><child/></parent></main>');
      $compare = new Compare($document->xpath());
      $this->assertEquals(
        1,
        $compare(
          $document->xpath()->firstOf('/*/*/child'),
          $document->xpath()->firstOf('/*/parent')
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Compare
     */
    public function testCompareNodeWithPreviousSibling() {
      $document = new Document();
      $document->loadXML('<main><previous/><next/></main>');
      $compare = new Compare($document->xpath());
      $this->assertEquals(
        1,
        $compare(
          $document->xpath()->firstOf('/*/next'),
          $document->xpath()->firstOf('/*/previous')
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Compare
     */
    public function testCompareNodeWithNextNode() {
      $document = new Document();
      $document->loadXML('<main><previous/><next/></main>');
      $compare = new Compare($document->xpath());
      $this->assertEquals(
        -1,
        $compare(
          $document->xpath()->firstOf('/*/previous'),
          $document->xpath()->firstOf('/*/next')
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Compare
     */
    public function testCompareNodesByPositionUsingXpath() {
      $document = new Document();
      $document->loadXML('<main><previous/><current/><next/></main>');
      $compare = new Compare($document->xpath());
      $this->assertEquals(
        -2,
        $compare(
          $document->xpath()->firstOf('/*/previous'),
          $document->xpath()->firstOf('/*/next')
        )
      );
    }
  }
}
