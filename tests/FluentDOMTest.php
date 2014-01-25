<?php

require_once(__DIR__.'/../src/FluentDOM.php');

class FluentDOMTest extends \PHPUnit_Framework_TestCase {

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::Query
   */
  public function testQuery() {
    $query = FluentDOM::Query();
    $this->assertInstanceOf('FluentDOM\Query', $query);
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::Query
   */
  public function testQueryWithNode() {
    $dom = new DOMDocument();
    $dom->appendChild($dom->createElement('test'));
    $query = FluentDOM::Query($dom->documentElement);
    $this->assertCount(1, $query);
    $this->assertEquals("<?xml version=\"1.0\"?>\n<test/>\n", (string)$query);
  }
}