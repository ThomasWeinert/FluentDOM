<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;
  use FluentDOM\TestCase;
  use FluentDOM\Xpath\Transformer;

  require_once(__DIR__.'/../TestCase.php');

  class QuerySelectorTest extends TestCase {

    /**
     * @cover FluentDOM\Node\QuerySelector\Implementation
     */
    public function testQuerySelector() {
      $transformer = $this->getMockBuilder(Transformer::class)->getMock();
      $transformer
        ->expects($this->once())
        ->method('toXpath')
        ->with('p', TRUE, FALSE)
        ->will($this->returnValue('//p'));

      \FluentDOM::registerXpathTransformer($transformer, TRUE);
      $document = new Document();
      $document->loadHTML(self::HTML);
      $this->assertEquals(
        '<p>Paragraph One</p>',
        $document->querySelector('p')->saveHtml()
      );
    }

    /**
     * @cover FluentDOM\Node\QuerySelector\Implementation
     */
    public function testQuerySelectorAll() {
      $transformer = $this->getMockBuilder(Transformer::class)->getMock();
      $transformer
        ->expects($this->once())
        ->method('toXpath')
        ->with('p', TRUE, FALSE)
        ->will($this->returnValue('//p'));

      \FluentDOM::registerXpathTransformer($transformer, TRUE);
      $document = new Document();
      $document->loadHTML(self::HTML);
      $this->assertEquals(
        '<p>Paragraph One</p><p>Paragraph Two</p>',
        $document->toHtml($document->querySelectorAll('p'))
      );
    }
  }
}