<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\Text {

  use FluentDOM\Loader\Options;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class CSVTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testSupportsExpectingTrue(): void {
      $loader = new CSV();
      $this->assertTrue($loader->supports('text/csv'));
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testSupportsExpectingFalse(): void {
      $loader = new CSV();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoad(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<json:json xmlns:json="urn:carica-json-dom.2013" json:type="array">
          <_>
            <one>1</one>
            <two>2</two>
            <three>3</three>
          </_>
        </json:json>',
        $loader->load(
          "one,two,three\r\n1,2,3", 'text/csv'
        )->getDocument()->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadFromFile(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<json:json xmlns:json="urn:carica-json-dom.2013" json:type="array">
          <_>
            <one>1</one>
            <two>2</two>
            <three>3</three>
          </_>
        </json:json>',
        $loader->load(
          __DIR__.'/TestData/example.csv', 'text/csv', [Options::IS_FILE => TRUE]
        )->getDocument()->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadFromArray(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<json:json xmlns:json="urn:carica-json-dom.2013" json:type="array">
          <_>
            <one>1</one>
            <two>2</two>
            <three>3</three>
          </_>
        </json:json>',
        $loader->load(
          [['one','two','three'],[1,2,3]], 'text/csv'
        )->getDocument()->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadFromTraversable(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<json:json xmlns:json="urn:carica-json-dom.2013" json:type="array">
          <_>
            <one>1</one>
            <two>2</two>
            <three>3</three>
          </_>
        </json:json>',
        $loader->load(
          new \ArrayIterator([['one','two','three'],[1,2,3]]), 'text/csv'
        )->getDocument()->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadWithDefinedFields(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<json:json xmlns:json="urn:carica-json-dom.2013" json:type="array">
          <_>
            <one>1</one>
            <two>2</two>
            <three>3</three>
          </_>
          <_>
            <one>4</one>
            <two>5</two>
            <three>6</three>
          </_>
        </json:json>',
        $loader->load(
          "1,2,3\r\n4,5,6",
          'text/csv',
          [
            'FIELDS' => [
              'one', 'two', 'three'
            ]
          ]
        )->getDocument()->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadNormalizesNames(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<json:json xmlns:json="urn:carica-json-dom.2013" json:type="array">
          <_>
            <oneone json:name="one+one">1</oneone>
            <twotwo json:name="two=two">2</twotwo>
            <threethree json:name="three three">3</threethree>
          </_>
        </json:json>',
        $loader->load(
          "one+one,two=two,three three\r\n1,2,3", 'text/csv'
        )->getDocument()->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadWithoutColumnNames(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<json:json xmlns:json="urn:carica-json-dom.2013" json:type="array">
          <_>
            <_ json:name="0">1</_>
            <_ json:name="1">2</_>
            <_ json:name="2">3</_>
          </_>
          <_>
            <_ json:name="0">4</_>
            <_ json:name="1">5</_>
            <_ json:name="2">6</_>
          </_>
        </json:json>',
        $loader->load(
          "1,2,3\n4,5,6", 'text/csv', ['HEADER' => FALSE]
        )->getDocument()->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new CSV();
      $this->assertNull(
        $loader->load(FALSE, 'text/csv')
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadFragment(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<fragment>
          <_>
            <_ xmlns:json="urn:carica-json-dom.2013" json:name="0">1</_>
            <_ xmlns:json="urn:carica-json-dom.2013" json:name="1">2</_>
            <_ xmlns:json="urn:carica-json-dom.2013" json:name="2">3</_>
          </_>
          <_>
            <_ xmlns:json="urn:carica-json-dom.2013" json:name="0">4</_>
            <_ xmlns:json="urn:carica-json-dom.2013" json:name="1">5</_>
            <_ xmlns:json="urn:carica-json-dom.2013" json:name="2">6</_>
          </_>
        </fragment>',
        '<fragment>'.
        $loader->loadFragment("1,2,3\n4,5,6", 'text/csv')->saveXmlFragment().
        '</fragment>'
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadFragmentWithColumnNames(): void {
      $loader = new CSV();
      $this->assertXmlStringEqualsXmlString(
        '<fragment>
          <_>
            <a>1</a>
            <b>2</b>
            <c>3</c>
          </_>
          <_>
            <a>4</a>
            <b>5</b>
            <c>6</c>
          </_>
        </fragment>',
        '<fragment>'.
        $loader->loadFragment("1,2,3\n4,5,6", 'text/csv', ['FIELDS' => ['a', 'b', 'c']])->saveXmlFragment().
        '</fragment>'
      );
    }

    /**
     * @covers \FluentDOM\Loader\Text\CSV
     */
    public function testLoadFragmentWithInvalidContentTypeExpectingNull(): void {
      $loader = new CSV();
      $this->assertNull(
        $loader->loadFragment("1,2,3\n4,5,6", 'INVALID', ['FIELDS' => ['a', 'b', 'c']])
      );
    }
  }
}
