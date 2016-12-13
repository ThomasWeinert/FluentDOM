<?php


namespace FluentDOM\Loader\Supports {

  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\Loader\Options;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class Libxml_TestProxy {

    use Libxml;

    public function getSupported() {
      return ['xml'];
    }

    public function load($source, $contentType, $options = []) {
      return $this->loadXmlDocument($source, $contentType, $options);
    }

  }

  class LibxmlTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\Supports\Libxml
     */
    public function testGetOptions() {
      $support = new Libxml_TestProxy();
      $options = $support->getOptions([]);
      $this->assertInstanceOf(Options::class, $options);
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Libxml
     */
    public function testLoadXmlString() {
      $support = new Libxml_TestProxy();
      $document = $support->load('<foo/>', 'xml');
      $this->assertEquals('foo', $document->documentElement->localName);
    }

    /**
     * @covers \FluentDOM\Loader\Supports\Libxml
     */
    public function testLoadXmlFile() {
      $support = new Libxml_TestProxy();
      $document = $support->load(__DIR__.'/TestData/loader.xml', 'xml', [Options::IS_FILE => TRUE]);
      $this->assertEquals('foo', $document->documentElement->localName);
    }
  }
}