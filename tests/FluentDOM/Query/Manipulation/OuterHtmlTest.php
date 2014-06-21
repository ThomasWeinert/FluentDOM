<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationOuterHtmlTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query
     */
    public function testOuterHtmlRead() {
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
     * @covers FluentDOM\Query
     */
    public function testOuterHtmlReadWithTextNodes() {
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
     * @covers FluentDOM\Query
     */
    public function testOuterHtmlReadEmpty() {
      $html = $this
        ->getQueryFixtureFromString('<html/>')
        ->find('/html/*')
        ->outerHtml();
      $this->assertEquals('', $html);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query
     */
    public function testOuterHtmlWrite() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p[position() = last()]')
        ->outerHtml('<b>New</b>World');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query
     */
    public function testOuterHtmlWriteEmpty() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $fd
        ->find('//p')
        ->outerHtml('');
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query
     */
    public function testOuterHtmlWriteWithCallback() {
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
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $fd);
    }

    /**
     * @group Manipulation
     * @group ManipulationReplace
     * @covers FluentDOM\Query
     */
    public function testOuterHtmlWriteWithInvalidDataExpectingException() {
      $fd = new Query();
      $this->setExpectedException('UnexpectedValueException');
      $fd->outerHtml(new \stdClass());
    }
  }
}