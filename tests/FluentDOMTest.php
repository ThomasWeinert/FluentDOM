<?php

require_once(__DIR__.'/../vendor/autoload.php');

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

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::create
   */
  public function testCreator() {
    $write = FluentDOM::create();
    $this->assertInstanceOf('FluentDOM\Nodes\Creator', $write);
    $this->assertEquals(
      "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<test/>\n",
      (string)$write('test')
    );
  }

  /**
   * @group FactoryFunctions
   * @covers FluentDOM::create
   */
  public function testCreatorWithArguments() {
    $write = FluentDOM::create('2.0', 'ASCII');
    $this->assertInstanceOf('FluentDOM\Nodes\Creator', $write);
    $this->assertEquals(
      "<?xml version=\"2.0\" encoding=\"ASCII\"?>\n<test/>\n",
      (string)$write('test')
    );
  }
}