<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class ManipulationHtmlTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testHtmlRead(): void {
      $query = $this
        ->getQueryFixtureFromString(
          '<html><body><p>Paragraph One</p>'."\n".'<p>Paragraph Two</p></body></html>'
        )
        ->find('//body');
      $this->assertEquals(
        "<p>Paragraph One</p>\n<p>Paragraph Two</p>",
        $query->html()
      );
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testHtmlReadEmpty(): void {
      $query = $this
        ->getQueryFixtureFromString('<html/>')
        ->find('/html/*');
      $this->assertEquals('', $query->html());
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testHtmlWrite(): void {
      $query = $this
        ->getQueryFixtureFromString(
          '<html><body><p>Paragraph One</p><p>Paragraph Two</p></body></html>'
        )
        ->find('//body')
        ->html('Hello <b>World!</b>');
      $this->assertInstanceOf(Query::class, $query);
      $this->assertEquals(
        '<html><body>Hello <b>World!</b></body></html>'."\n",
        $query->document->saveHtml()
      );
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers \FluentDOM\Query
     */
    public function testHtmlWriteUsingCallback(): void {
      $query = $this
        ->getQueryFixtureFromString(
          '<html><body><p>Paragraph One</p><p>Paragraph Two</p></body></html>'
        )
        ->find('//body/p')
        ->html(
          function($node) {
            return '<b>'.$node->nodeValue.'</b>';
          }
        );
      $this->assertInstanceOf(Query::class, $query);
      $this->assertEquals(
        '<html><body><p><b>Paragraph One</b></p><p><b>Paragraph Two</b></p></body></html>'."\n",
        $query->document->saveHtml()
      );
    }
  }
}
