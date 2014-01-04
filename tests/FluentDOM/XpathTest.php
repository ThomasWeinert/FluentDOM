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
      $dom = new \DOMDocument();
      $xpath = new Xpath($dom);
      $this->setExpectedException('PHPUnit_Framework_Error_Deprecated');
      $xpath->query('*');
    }

    /**
     * @covers FluentDOM\Xpath
     * @dataProvider provideValuesForQuote
     * @param $value
     */
    public function testQuote($value) {
      $xml = '<node value="'.htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8').'"/>';
      $dom = new \DOMDocument();
      $dom->loadXML($xml);
      $xpath = new Xpath($dom);
      $this->assertEquals(
        1,
        $xpath->evaluate('count(//node[@value = '.$xpath->quote($value).'])')
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

    /***************************
     * Data Provider
     **************************/

    /**
     * @return array
     */
    public static function provideValuesForQuote() {
      return [
        'simple string' => ['foo'],
        'single quote' => ["'"],
        'double quote' => ['"'],
        'quotes' => ['\'"'],
        'complex quotes' => ['O\'Haras "Hello World!"']
      ];
    }
  }
}