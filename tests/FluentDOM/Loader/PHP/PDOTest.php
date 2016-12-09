<?php
namespace FluentDOM\Loader\PHP {

  use FluentDOM\Exceptions\InvalidFragmentLoader;
  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class PDOTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testSupportsExpectingTrue() {
      $loader = new PDO();
      $this->assertTrue($loader->supports('php/pdo'));
    }

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testSupportsExpectingFalse() {
      $loader = new PDO();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testLoad() {
      if (!(extension_loaded('pdo') && extension_loaded('pdo_sqlite'))) {
        $this->markTestSkipped('PDO/Sqlite is needed for this test');
      }
      $pdo = $this->getExampleDatabase();
      $loader = new PDO();
      $this->assertXmlStringEqualsXmlString(
        '<json:json xmlns:json="urn:carica-json-dom.2013">
          <_>
            <id>1</id>
            <givenname>Alice</givenname>
          </_>
          <_>
            <id>2</id>
            <givenname>Bob</givenname>
          </_>
        </json:json>',
        $loader->load($pdo->query("SELECT * FROM persons"), 'php/pdo')->getDocument()->saveXML()
      );
    }

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new PDO();
      $this->assertNull(
        $loader->load(new \stdClass(), 'php/pdo')
      );
    }

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testLoadFragmentExpectingException() {
      $loader = new PDO();
      $this->expectException(InvalidFragmentLoader::class);
      $loader->loadFragment(NULL, 'php/pdo');
    }

    private function getExampleDatabase() {
      $pdo = new \PDO('sqlite::memory:');
      $pdo->query(
        "CREATE TABLE persons(
           id INT PRIMARY KEY NOT NULL,
           givenname TEXT NOT NULL
        )"
      );
      $pdo->query(
        "INSERT INTO persons (id, givenname) VALUES (1, 'Alice')"
      );
      $pdo->query(
        "INSERT INTO persons (id, givenname) VALUES (2, 'Bob')"
      );
      return $pdo;
    }
  }
}