<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\PHP {

  use FluentDOM\Exceptions\InvalidFragmentLoader;
  use FluentDOM\TestCase;

  require_once __DIR__ . '/../../TestCase.php';

  class PDOTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testSupportsExpectingTrue(): void {
      $loader = new PDO();
      $this->assertTrue($loader->supports('php/pdo'));
    }

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testSupportsExpectingFalse(): void {
      $loader = new PDO();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testLoad(): void {
      if (!(extension_loaded('pdo') && extension_loaded('pdo_sqlite'))) {
        $this->markTestSkipped('PDO/Sqlite is needed for this test');
      }
      $pdo = $this->getExampleDatabase();
      $loader = new PDO();
      $pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, TRUE);
      $xml = $loader->load(
        $pdo->query("SELECT * FROM persons"), 'php/pdo'
      )->getDocument()->saveXML();
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
        $xml
      );
    }

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testLoadWithInvalidSourceExpectingNull(): void {
      $loader = new PDO();
      $this->assertNull(
        $loader->load(new \stdClass(), 'php/pdo')
      );
    }

    /**
     * @covers \FluentDOM\Loader\PHP\PDO
     */
    public function testLoadFragmentExpectingException(): void {
      $loader = new PDO();
      $this->expectException(InvalidFragmentLoader::class);
      $loader->loadFragment(NULL, 'php/pdo');
    }

    private function getExampleDatabase(): \PDO {
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
