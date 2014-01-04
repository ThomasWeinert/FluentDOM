<?php
namespace FluentDOM {

  require_once(__DIR__.'/TestCase.php');

  class XpathTest extends TestCase {

    public function testRegisterNamespaceRegistersOnDocument() {
      $dom = $this->getMock('FluentDOM\\Document');
      $dom
        ->expects($this->once())
        ->method('registerNamespace')
        ->with("bar", "urn:foo");
      $xpath = new Xpath($dom);
      $this->assertTrue($xpath->registerNamespace("bar", "urn:foo"));
    }
  }
}