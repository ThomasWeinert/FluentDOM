<?php
namespace FluentDOM {

  require_once(__DIR__.'/../src/_require.php');

  class QueryTest extends \PHPUnit_Framework_TestCase {

    const XML = '
      <items version="1.0">
        <group id="1st">
          <item index="0">text1</item>
          <item index="1">text2</item>
          <item index="2">text3</item>
        </group>
        <html>
          <div class="test1 test2">class testing</div>
          <div class="test2">class testing</div>
        </html>
      </items>
    ';

    /**
     * @group CoreFunctions
     * @covers Query::spawn
     */
    public function testSpawn() {
      $fdParent = new Query;
      $fdChild = $fdParent->spawn();
      $this->assertAttributeSame(
        $fdParent,
        '_parent',
        $fdChild
      );
    }

    /**
     * @group CoreFunctions
     * @covers Query::spawn
     */
    public function testSpawnWithElements() {
      $dom = new \DOMDocument;
      $node = $dom->createElement('test');
      $dom->appendChild($node);
      $fdParent = new Query();
      $fdParent->load($dom);
      $fdChild = $fdParent->spawn($node);
      $this->assertSame(
        array($node),
        iterator_to_array($fdChild)
      );
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetExists
     *
     */
    public function testOffsetExistsExpectingTrue() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertTrue(isset($query[1]));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetExists
     *
     */
    public function testOffsetExistsExpectingFalse() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertFalse(isset($query[99]));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetGet
     */
    public function testOffsetGet() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertEquals('text2', $query[1]->nodeValue);
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetGet
     */
    public function testOffsetSetExpectingException() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->setExpectedException('BadMethodCallException');
      $query[2] = '123';
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetGet
     */
    public function testOffsetUnsetExpectingException() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->setExpectedException('BadMethodCallException');
      unset($query[2]);
    }

    /******************************
     * Fixtures
     ******************************/

    function getQueryFixtureFromString($string = NULL, $xpath = NULL) {
      $fd = new Query();
      if (!empty($string)) {
        $dom = new \DOMDocument();
        $dom->loadXML($string);
        $fd->load($dom);
        if (!empty($xpath)) {
          $query = new Xpath($dom);
          $nodes = $query->evaluate($xpath);
          $fd = $fd->spawn();
          $fd->push($nodes);
        }
      }
      return $fd;
    }
  }
}