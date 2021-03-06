<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\DOM {

  require_once __DIR__ . '/../TestCase.php';

  use FluentDOM\TestCase;
  use FluentDOM\Utility\Namespaces;

  class XpathTest extends TestCase {

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testRegisterNamespaceRegistersOnDocument(): void {
      $namespaces = $this->createMock(Namespaces::class);
      $namespaces
        ->expects($this->once())
        ->method('offsetExists')
        ->with("bar")
        ->willReturn(FALSE);
      $document = $this->getMockBuilder(Document::class)->getMock();
      $document
        ->expects($this->once())
        ->method('namespaces')
        ->willReturn($namespaces);
      $document
        ->expects($this->once())
        ->method('registerNamespace')
        ->with("bar", "urn:foo");
      $xpath = new Xpath($document);
      $this->assertTrue($xpath->registerNamespace("bar", "urn:foo"));
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testRegisterNamespaceRegistersOverwritesExisting(): void {
      $namespaces = $this->createMock(Namespaces::class);
      $namespaces
        ->expects($this->once())
        ->method('offsetExists')
        ->with("bar")
        ->willReturn(TRUE);
      $namespaces
        ->expects($this->once())
        ->method('offsetGet')
        ->with("bar")
        ->willReturn("urn:bar");
      $document = $this->getMockBuilder(Document::class)->getMock();
      $document
        ->method('namespaces')
        ->willReturn($namespaces);
      $document
        ->expects($this->once())
        ->method('registerNamespace')
        ->with("bar", "urn:foo");
      $xpath = new Xpath($document);
      $this->assertTrue($xpath->registerNamespace("bar", "urn:foo"));
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testEvaluateDoesNotRegisterNodeNamespaces(): void {
      $document = new \DOMDocument();
      $document->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($document);
      $xpath->registerNodeNamespaces = FALSE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:bar',
        $xpath->evaluate('string(//foo:child)')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testEvaluateRegisterNodeNamespaces(): void {
      $document = new \DOMDocument();
      $document->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($document);
      $xpath->registerNodeNamespaces = TRUE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:foo',
        $xpath->evaluate('string(//foo:child)')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testEvaluateDisableRegisterNodeNamespacesWithArgument(): void {
      $document = new \DOMDocument();
      $document->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($document);
      $xpath->registerNodeNamespaces = TRUE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:bar',
        $xpath->evaluate('string(//foo:child)', NULL, FALSE)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testMagicMethodInvoke(): void {
      $document = new \DOMDocument();
      $document->loadXml(self::XML);
      $xpath = new Xpath($document);
      $this->assertEquals('1st', $xpath('string(//group/@id)'));
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testMagicMethodInvokeWithContext(): void {
      $document = new \DOMDocument();
      $document->loadXml(self::XML);
      $xpath = new Xpath($document);
      $this->assertEquals('items', $xpath('name()', $document->documentElement));
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testQueryDoesNotRegisterNodeNamespaces(): void {
      $document = new \DOMDocument();
      $document->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($document);
      $xpath->registerNodeNamespaces = FALSE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:bar',
        @$xpath->query('//foo:child')->item(0)->nodeValue
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testQueryRegisterNodeNamespaces(): void {
      $document = new \DOMDocument();
      $document->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($document);
      $xpath->registerNodeNamespaces = TRUE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:foo',
        @$xpath->query('//foo:child')->item(0)->nodeValue
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testQueryGeneratesDeprecatedError(): void {
      $current = error_reporting();
      if (($current & E_USER_DEPRECATED) !== E_USER_DEPRECATED) {
        error_reporting($current | E_USER_DEPRECATED);
      }
      $document = new \DOMDocument();
      $xpath = new Xpath($document);
      $this->expectDeprecation();
      $xpath->query('*');
      error_reporting($current);
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testFirstOfMatchingNode(): void {
      $document = new \DOMDocument();
      $document->loadXml('<foo/>');
      $xpath = new Xpath($document);
      $this->assertSame(
        $document->documentElement,
        $xpath->firstOf('//foo')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testFirstOfMatchingNothingExpectingNull(): void {
      $document = new \DOMDocument();
      $document->loadXml('<foo/>');
      $xpath = new Xpath($document);
      $this->assertNull(
        $xpath->firstOf('//bar')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testFirstOfMatchingScalarExpectingNull(): void {
      $document = new \DOMDocument();
      $document->loadXml('<foo>bar</foo>');
      $xpath = new Xpath($document);
      $this->assertNull(
        $xpath->firstOf('string(//foo)')
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     * @dataProvider provideValuesForQuote
     * @param string $expected
     * @param string $value
     */
    public function testQuote(string $expected, string $value): void {
      $this->assertEquals(
        $expected,
        Xpath::quote($value)
      );
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testPropertyRegisterNodeNamespacesIsset(): void {
      $document = new \DOMDocument();
      $xpath = new Xpath($document);
      $this->assertTrue(isset($xpath->registerNodeNamespaces));
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testPropertyRegisterNodeNamespacesGetAfterSet(): void {
      $document = new \DOMDocument();
      $xpath = new Xpath($document);
      $this->assertFalse($xpath->registerNodeNamespaces);
      $xpath->registerNodeNamespaces = TRUE;
      $this->assertTrue($xpath->registerNodeNamespaces);
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testPropertyRegisterNodeNamespacesGetAfterUnset(): void {
      $document = new \DOMDocument();
      $xpath = new Xpath($document);
      $xpath->registerNodeNamespaces = TRUE;
      unset($xpath->registerNodeNamespaces);
      $this->assertFalse($xpath->registerNodeNamespaces);
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testDynamicProperty(): void {
      $document = new \DOMDocument();
      $xpath = new Xpath($document);
      $this->assertFalse(isset($xpath->foo));
      $xpath->foo = 'bar';
      $this->assertTrue(isset($xpath->foo));
      $this->assertEquals('bar', $xpath->foo);
      unset($xpath->foo);
      $this->assertFalse(isset($xpath->foo));
    }

    /**
     * @covers \FluentDOM\DOM\Xpath
     */
    public function testPropertyGetWithUnknownPropertyExpectingPHPError(): void {
      $errors = error_reporting(E_ALL);
      $document = new \DOMDocument();
      $xpath = new Xpath($document);
      if (PHP_VERSION_ID < 80000) {
        $this->expectNotice();
      } else {
        $this->expectWarning();
      }
      /** @noinspection PhpUndefinedFieldInspection */
      /** @noinspection PhpExpressionResultUnusedInspection */
      $xpath->someUnknownProperty;
      error_reporting($errors);
    }

    /***************************
     * Data Provider
     **************************/

    /**
     * @return array
     */
    public static function provideValuesForQuote(): array {
      return [
        'simple string' => ["'foo'", 'foo'],
        'single quote' => ['"\'"', "'"],
        'double quote' => ["'\"'", '"'],
        'quotes' => ['concat("\'", \'"\')', '\'"'],
        'complex quotes' => ['concat("O\'Haras ", \'"Hello World!"\')', 'O\'Haras "Hello World!"'],
        'null byte' => ["''", "\x00"]
      ];
    }
  }
}
