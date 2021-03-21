<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Node {

  use FluentDOM\TestCase;
  use FluentDOM\DOM\Document;
  use FluentDOM\Xpath\Transformer;

  require_once __DIR__.'/../../TestCase.php';

  class QuerySelectorTest extends TestCase {

    /**
     * @cover FluentDOM\Node\QuerySelector\Implementation
     */
    public function testQuerySelector(): void {
      $transformer = $this->createMock(Transformer::class);
      $transformer
        ->expects($this->once())
        ->method('toXpath')
        ->with('p', TRUE, FALSE)
        ->willReturn('//p');

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
    public function testQuerySelectorAll(): void {
      $transformer = $this->createMock(Transformer::class);
      $transformer
        ->expects($this->once())
        ->method('toXpath')
        ->with('p', TRUE, FALSE)
        ->willReturn('//p');

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
