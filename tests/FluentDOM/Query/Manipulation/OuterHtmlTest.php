<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query\Manipulation {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class OuterHtmlTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterHtmlRead(): void {
      $expect = '<p>Paragraph One</p>';
      $html = $this
        ->getQueryFixtureFromString(self::HTML)
        ->find('//p')
        ->outerHtml();
      $this->assertXmlStringEqualsXmlString($expect, $html);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterHtmlReadWithTextNodes(): void {
      $expect = 'Paragraph One';
      $html = $this
        ->getQueryFixtureFromString(self::HTML)
        ->find('//p[1]/text()')
        ->outerHtml();
      $this->assertEquals($expect, $html);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterHtmlReadEmpty(): void {
      $html = $this
        ->getQueryFixtureFromString('<html/>')
        ->find('/html/*')
        ->outerHtml();
      $this->assertEquals('', $html);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterHtmlWrite(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p[position() = last()]')
        ->outerHtml('<b>New</b>World');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterHtmlWriteEmpty(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->outerHtml('');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterHtmlWriteWithCallback(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->outerHtml(
          function($node, $index, $html) {
            if ($index == 1) {
              return '';
            } else {
              return strtoupper($html);
            }
          }
        );
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers \FluentDOM\Query
     */
    public function testOuterHtmlWriteWithInvalidDataExpectingException(): void {
      $fd = new Query();
      $this->expectException(\TypeError::class);
      $fd->outerHtml(new \stdClass());
    }
  }
}
