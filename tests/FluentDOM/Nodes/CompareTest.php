<?php

namespace FluentDOM\Nodes {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class CompareTest extends TestCase {

    /**
     * @covers FluentDOm\Nodes\Compare
     */
    public function testCompareDocumentElementWithChildNode() {
      $dom = new Document();
      $dom->loadXML('<main><child/></main>');
      $compare = new Compare($dom->xpath());
      $this->assertEquals(
        -1,
        $compare(
          $dom->xpath()->firstOf('/*'),
          $dom->xpath()->firstOf('/*/*')
        )
      );
    }

    /**
     * @covers FluentDOm\Nodes\Compare
     */
    public function testCompareChildNodeWithDocumentElement() {
      $dom = new Document();
      $dom->loadXML('<main><child/></main>');
      $compare = new Compare($dom->xpath());
      $this->assertEquals(
        1,
        $compare(
          $dom->xpath()->firstOf('/*/*'),
          $dom->xpath()->firstOf('/*')
        )
      );
    }

    /**
     * @covers FluentDOm\Nodes\Compare
     */
    public function testCompareDocumentElementWithItself() {
      $dom = new Document();
      $dom->loadXML('<main><child/></main>');
      $compare = new Compare($dom->xpath());
      $this->assertEquals(
        0,
        $compare(
          $dom->xpath()->firstOf('/*'),
          $dom->xpath()->firstOf('/*')
        )
      );
    }

    /**
     * @covers FluentDOm\Nodes\Compare
     */
    public function testCompareParentNodeWithChildNode() {
      $dom = new Document();
      $dom->loadXML('<main><parent><child/></parent></main>');
      $compare = new Compare($dom->xpath());
      $this->assertEquals(
        -1,
        $compare(
          $dom->xpath()->firstOf('/*/parent'),
          $dom->xpath()->firstOf('/*/*/child')
        )
      );
    }

    /**
     * @covers FluentDOm\Nodes\Compare
     */
    public function testCompareChildNodeWithParentNode() {
      $dom = new Document();
      $dom->loadXML('<main><parent><child/></parent></main>');
      $compare = new Compare($dom->xpath());
      $this->assertEquals(
        1,
        $compare(
          $dom->xpath()->firstOf('/*/*/child'),
          $dom->xpath()->firstOf('/*/parent')
        )
      );
    }

    /**
     * @covers FluentDOm\Nodes\Compare
     */
    public function testCompareNodeWithPreviousSibling() {
      $dom = new Document();
      $dom->loadXML('<main><previous/><next/></main>');
      $compare = new Compare($dom->xpath());
      $this->assertEquals(
        1,
        $compare(
          $dom->xpath()->firstOf('/*/next'),
          $dom->xpath()->firstOf('/*/previous')
        )
      );
    }

    /**
     * @covers FluentDOm\Nodes\Compare
     */
    public function testCompareNodeWithNextNode() {
      $dom = new Document();
      $dom->loadXML('<main><previous/><next/></main>');
      $compare = new Compare($dom->xpath());
      $this->assertEquals(
        -1,
        $compare(
          $dom->xpath()->firstOf('/*/previous'),
          $dom->xpath()->firstOf('/*/next')
        )
      );
    }

    /**
     * @covers FluentDOm\Nodes\Compare
     */
    public function testCompareNodesByPositionUsingXpath() {
      $dom = new Document();
      $dom->loadXML('<main><previous/><current/><next/></main>');
      $compare = new Compare($dom->xpath());
      $this->assertEquals(
        -2,
        $compare(
          $dom->xpath()->firstOf('/*/previous'),
          $dom->xpath()->firstOf('/*/next')
        )
      );
    }
  }
}
