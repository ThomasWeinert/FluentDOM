<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\LoaderSupports {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loader\LoaderOptions;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class Libxml_TestProxy {

    use LibxmlSupports;

    public function getSupported(): array {
      return ['xml'];
    }

    public function load($source, $contentType, $options = []): Document {
      return $this->loadXmlDocument($source, $options);
    }

  }

  class LibxmlSupportsTest extends TestCase {

    /**
     * @covers \FluentDOM\Loader\LoaderSupports\LibxmlSupports
     */
    public function testGetOptions(): void {
      $support = new Libxml_TestProxy();
      $options = $support->getOptions([]);
      $this->assertInstanceOf(LoaderOptions::class, $options);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderSupports\LibxmlSupports
     */
    public function testLoadXmlString(): void {
      $support = new Libxml_TestProxy();
      $document = $support->load('<foo/>', 'xml');
      $this->assertEquals('foo', $document->documentElement->localName);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderSupports\LibxmlSupports
     */
    public function testLoadXmlFile(): void {
      $support = new Libxml_TestProxy();
      $document = $support->load(__DIR__.'/TestData/loader.xml', 'xml', [LoaderOptions::IS_FILE => TRUE]);
      $this->assertEquals('foo', $document->documentElement->localName);
    }
  }
}
