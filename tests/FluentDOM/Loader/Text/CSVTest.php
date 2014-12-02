<?php
namespace FluentDOM\Loader\Text {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class CSVTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Text\CSV
     */
    public function testSupportsExpectingTrue() {
      $loader = new CSV();
      $this->assertTrue($loader->supports('text/csv'));
    }

    /**
     * @covers FluentDOM\Loader\Text\CSV
     */
    public function testSupportsExpectingFalse() {
      $loader = new CSV();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\Text\CSV
     */
    public function testLoad() {
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
        )->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Loader\Text\CSV
     */
    public function testLoadWithDefinedFields() {
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
        )->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Loader\Text\CSV
     */
    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new CSV();
      $this->assertNull(
        $loader->load(FALSE, 'text/csv')
      );
    }
  }
}