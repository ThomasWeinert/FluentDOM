<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class XpathTest extends TestCase {

    /**
     * @covers FluentDOM\Xpath
     */
    public function testRegisterNamespaceRegistersOnDocument() {
      $dom = $this->getMock('FluentDOM\\Document');
      $dom
        ->expects($this->once())
        ->method('registerNamespace')
        ->with("bar", "urn:foo");
      $xpath = new Xpath($dom);
      $this->assertTrue($xpath->registerNamespace("bar", "urn:foo"));
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testEvaluateDoesNotRegisterNodeNamespaces() {
      $dom = new \DOMDocument();
      $dom->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($dom);
      $xpath->registerNodeNamespaces = FALSE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:bar',
        $xpath->evaluate('string(//foo:child)')
      );
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testEvaluateRegisterNodeNamespaces() {
      $dom = new \DOMDocument();
      $dom->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($dom);
      $xpath->registerNodeNamespaces = TRUE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:foo',
        $xpath->evaluate('string(//foo:child)')
      );
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testEvaluateDisableRegisterNodeNamespacesWithArgument() {
      $dom = new \DOMDocument();
      $dom->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($dom);
      $xpath->registerNodeNamespaces = TRUE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:bar',
        $xpath->evaluate('string(//foo:child)', NULL, FALSE)
      );
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testMagicMethodInvoke() {
      $dom = new \DOMDocument();
      $dom->loadXml(self::XML);
      $xpath = new Xpath($dom);
      $this->assertEquals('1st', $xpath('string(//group/@id)'));
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testMagicMethodInvokeWithContext() {
      $dom = new \DOMDocument();
      $dom->loadXml(self::XML);
      $xpath = new Xpath($dom);
      $this->assertEquals('items', $xpath('name()', $dom->documentElement));
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testQueryDoesNotRegisterNodeNamespaces() {
      $dom = new \DOMDocument();
      $dom->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($dom);
      $xpath->registerNodeNamespaces = FALSE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:bar',
        @$xpath->query('//foo:child')->item(0)->nodeValue
      );
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testQueryRegisterNodeNamespaces() {
      $dom = new \DOMDocument();
      $dom->loadXml(
        '<foo:root xmlns:foo="urn:foo">
          <foo:child>found urn:foo</foo:child>
          <bar:child xmlns:bar="urn:bar">found urn:bar</bar:child>
        </foo:root>'
      );
      $xpath = new Xpath($dom);
      $xpath->registerNodeNamespaces = TRUE;
      $xpath->registerNamespace('foo', 'urn:bar');
      $this->assertEquals(
        'found urn:foo',
        @$xpath->query('//foo:child')->item(0)->nodeValue
      );
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testQueryGeneratesDeprecatedError() {
      $current = error_reporting();
      if (($current & E_USER_DEPRECATED) != E_USER_DEPRECATED) {
        error_reporting($current | E_USER_DEPRECATED);
      }
      $dom = new \DOMDocument();
      $xpath = new Xpath($dom);
      $this->setExpectedException('PHPUnit_Framework_Error_Deprecated');
      $xpath->query('*');
      error_reporting($current);
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testFirstOfMatchingNode() {
      $dom = new \DOMDocument();
      $dom->loadXml('<foo/>');
      $xpath = new Xpath($dom);
      $this->assertSame(
        $dom->documentElement,
        $xpath->firstOf('//foo')
      );
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testFirstOfMatchingNothingExpectingNull() {
      $dom = new \DOMDocument();
      $dom->loadXml('<foo/>');
      $xpath = new Xpath($dom);
      $this->assertNull(
        $xpath->firstOf('//bar')
      );
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testFirstOfMatchingScalarExpectingNull() {
      $dom = new \DOMDocument();
      $dom->loadXml('<foo>bar</foo>');
      $xpath = new Xpath($dom);
      $this->assertNull(
        $xpath->firstOf('string(//foo)')
      );
    }

    /**
     * @covers FluentDOM\Xpath
     * @dataProvider provideValuesForQuote
     * @param string $expected
     * @param string $value
     */
    public function testQuote($expected, $value) {
      $dom = new \DOMDocument();
      $xpath = new Xpath($dom);
      $this->assertEquals(
        $expected,
        $xpath->quote($value)
      );
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testPropertyRegisterNodeNamespacesIsset() {
      $dom = new \DOMDocument();
      $xpath = new Xpath($dom);
      $this->assertTrue(isset($xpath->registerNodeNamespaces));
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testPropertyRegisterNodeNamespacesGetAfterSet() {
      $dom = new \DOMDocument();
      $xpath = new Xpath($dom);
      $this->assertFalse($xpath->registerNodeNamespaces);
      $xpath->registerNodeNamespaces = TRUE;
      $this->assertTrue($xpath->registerNodeNamespaces);
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testPropertyRegisterNodeNamespacesGetAfterUnset() {
      $dom = new \DOMDocument();
      $xpath = new Xpath($dom);
      $xpath->registerNodeNamespaces = TRUE;
      unset($xpath->registerNodeNamespaces);
      $this->assertFalse($xpath->registerNodeNamespaces);
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testDynamicProperty() {
      $dom = new \DOMDocument();
      $xpath = new Xpath($dom);
      $this->assertFalse(isset($xpath->foo));
      $xpath->foo = 'bar';
      $this->assertTrue(isset($xpath->foo));
      $this->assertEquals('bar', $xpath->foo);
      unset($xpath->foo);
      $this->assertFalse(isset($xpath->foo));
    }

    /**
     * @covers FluentDOM\Xpath
     */
    public function testPropertyGetWithUnknownPropertyExpectingPHPError() {
      $errors = error_reporting(E_ALL);
      $dom = new \DOMDocument();
      $xpath = new Xpath($dom);
      $this->setExpectedException('PHPUnit_Framework_Error_Notice');
      $xpath->someUnknownProperty;
      error_reporting($errors);
    }

    /***************************
     * Data Provider
     **************************/

    /**
     * @return array
     */
    public static function provideValuesForQuote() {
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