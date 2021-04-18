<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\Supports {

  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\Loader\Options;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class Json_TestProxy {

    use Json;

    public function getSupported(): array {
      return ['json'];
    }

    /**
     * @throws InvalidSource
     */
    public function getSource($source, $type = 'json', $options = []) {
      return $this->getJson($source, $type, $options);
    }

    public function getValue($json): string {
      return $this->getValueAsString($json);
    }

    protected function transferTo(\DOMNode $target, $json): void {
      $document = $target->ownerDocument ?: $target;
      $target->appendChild($document->createElement('success'));
    }

    public function getNamespace($nodeName, $namespaces, $parent): ?string {
      return $this->getNamespaceForNode($nodeName, $namespaces, $parent);
    }

  }

  class JsonTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithArrayAsString(): void {
      $loader = new Json_TestProxy();
      $this->assertEquals(['foo'], $loader->getSource(json_encode(['foo'])));
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithObjectAsString(): void {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource(json_encode($json)));
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithObject(): void {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource($json));
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithFileAllowFile(): void {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals(
        $json,
        $loader->getSource(__DIR__.'/TestData/loader.json', 'json', [ Options::ALLOW_FILE => TRUE ])
      );
    }

    /**
     * @covers \FluentDOM\Loader\Json\JsonDOM
     */
    public function testGetSourceWithFileExpectingException(): void {
      $loader = new Json_TestProxy();
      $this->expectException(InvalidSource\TypeFile::class);
      $loader->getSource(__DIR__.'/TestData/loader.json');
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithUnsupportedTypeExpectingFalse(): void {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertFalse($loader->getSource($json, 'invalid'));
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithInvalidJsonExpectingException(): void {
      $loader = new Json_TestProxy();
      $this->expectException(LoadingError\Json::class);
      $loader->getSource('{invalid');
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testLoad(): void {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $document = $loader->load($json, 'json')->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<success/>', $document->saveXml()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testLoadExpectingNull(): void {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertNull(
        $loader->load(NULL, 'json')
      );
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testLoadFragment(): void {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $fragment = $loader->loadFragment($json, 'json');
      $this->assertXmlStringEqualsXmlString(
        '<success/>', $fragment->saveXmlFragment()
      );
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testLoadFragmentExpectingNull(): void {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertNull(
        $loader->loadFragment(NULL, 'json')
      );
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testGetNamespaceForNodeFromNode(): void {
      $document = new \DOMDocument();
      $document->loadXml('<foo:foo xmlns:foo="urn:foo"/>');
      $loader = new Json_TestProxy();
      $this->assertEquals(
        'urn:foo',
        $loader->getNamespace(
          'foo:bar', new \stdClass(), $document->documentElement
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     */
    public function testGetNamespaceForNodeFromJsonProperties(): void {
      $document = new \DOMDocument();
      $document->loadXml('<foo:foo xmlns:foo="urn:foo"/>');
      $properties = new \stdClass();
      $properties->{'xmlns:foo'} = 'urn:bar';
      $loader = new Json_TestProxy();
      $this->assertEquals(
        'urn:bar',
        $loader->getNamespace(
          'foo:bar', $properties, $document->documentElement
        )
      );
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Json
     * @dataProvider provideJsonValues
     */
    public function testGetValueAsJson(string $expected, $value): void {
      $loader = new Json_TestProxy();
      $this->assertSame(
        $expected,
        $loader->getValue($value)
      );
    }

    public static function provideJsonValues(): array {
      return [
        ['true', TRUE],
        ['false', FALSE],
        ['', ''],
        ['foo', 'foo'],
        ['42', 42],
        ['42.21', 42.21]
      ];
    }
  }
}
