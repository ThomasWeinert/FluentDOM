<?php
namespace FluentDOM {

  require_once(__DIR__.'/../src/_require.php');

  class QueryTest extends \PHPUnit_Framework_TestCase {

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

  }
}