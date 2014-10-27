<?php

namespace FluentDOM\Node {

  use FluentDOM\Document;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class ParentNodeTest extends TestCase {

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     */
    public function testPrepend() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $dom->documentElement->prepend('INSERTED');
      $this->assertXmlStringEqualsXmlString(
        '<foo>INSERTED<bar/></foo>',
        $dom->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Node\ParentNode\Implementation
     */
    public function testAppend() {
      $dom = new Document();
      $dom->loadXML('<foo><bar/></foo>');
      $dom->documentElement->append('APPENDED');
      $this->assertXmlStringEqualsXmlString(
        '<foo><bar/>APPENDED</foo>',
        $dom->saveXML()
      );
    }
  }
}