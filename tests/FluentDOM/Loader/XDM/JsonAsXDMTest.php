<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader\XDM {

  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\Loader\Options;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  /**
   * @covers \FluentDOM\Loader\XDM\JsonAsXDM
   */
  class JsonAsXDMTest extends TestCase {

    public function testSupportsExpectingTrue() {
      $loader = new JsonAsXDM();
      $this->assertTrue($loader->supports('xdm-json'));
    }

    public function testSupportsExpectingFalse() {
      $loader = new JsonAsXDM();
      $this->assertFalse($loader->supports('text/xml'));
    }

    public function testLoadWithValidJson() {
      $loader = new JsonAsXDM();
      $this->assertInstanceOf(
        'DOMDocument',
        $document = $loader->load(
          '{"foo":"bar"}',
          'xdm-json'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<map xmlns="http://www.w3.org/2005/xpath-functions">'.
        '  <string key="foo">bar</string>'.
        '</map>',
        $document->saveXml()
      );
    }

    public function testLoadWithValidFileAllowFile() {
      $loader = new JsonAsXDM();
      $this->assertInstanceOf(
        'DOMDocument',
        $document = $loader->load(
          __DIR__.'/TestData/loader.json',
          'xdm-json',
          [
            Options::ALLOW_FILE => TRUE
          ]
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<map xmlns="http://www.w3.org/2005/xpath-functions">'.
        '  <string key="foo">bar</string>'.
        '</map>',
        $document->saveXml()
      );
    }

    public function testLoadWithValidFileExpectingException() {
      $loader = new JsonAsXDM();
      $this->expectException(InvalidSource\TypeFile::class);
      $loader->load(
        __DIR__.'/TestData/loader.json',
        'xdm-json'
      );
    }

    public function testLoadWithValidStructure() {
      $loader = new JsonAsXDM();
      $json = new \stdClass();
      $json->foo = 'bar';
      $this->assertInstanceOf(
        'DOMDocument',
        $document = $loader->load(
          $json, 'xdm-json'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<map xmlns="http://www.w3.org/2005/xpath-functions">'.
        '  <string key="foo">bar</string>'.
        '</map>',
        $document->saveXml()
      );
    }

    public function testLoadWithAllTypes() {
      $loader = new JsonAsXDM();
      $json = json_decode(
        '{
          "_id":"53e3c6ed-9bfc-2730-e053-0100007f6afb",
          "content":{
            "name":"object one",
            "type":1,
            "isNew":true,
            "clientId":null,
            "values":[
              {"name":"x", "v":1},
              {"name":"y", "v":2}
            ]
          }
        }',
        FALSE
      );
      $this->assertInstanceOf(
        'DOMDocument',
        $document = $loader->load(
          $json, 'xdm-json'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>
        <map xmlns="http://www.w3.org/2005/xpath-functions">
          <string key="_id">53e3c6ed-9bfc-2730-e053-0100007f6afb</string>
          <map key="content">
            <string key="name">object one</string>
            <number key="type">1</number>
            <boolean key="isNew">true</boolean>
            <null key="clientId"/>
            <array key="values">
              <map>
                <string key="name">x</string>
                <number key="v">1</number>
              </map>
              <map>
                <string key="name">y</string>
                <number key="v">2</number>
              </map>
            </array>
          </map>
        </map>',
        $document->saveXml()
      );
    }

    public function testLoadWithAssociativeArray() {
      $loader = new JsonAsXDM();
      $json = ['foo' => 'bar'];
      $this->assertInstanceOf(
        'DOMDocument',
        $document = $loader->load(
          $json, 'xdm-json'
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>'.
        '<map xmlns="http://www.w3.org/2005/xpath-functions">'.
        '  <string key="foo">bar</string>'.
        '</map>',
        $document->saveXml()
      );
    }

    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new JsonAsXDM();
      $this->assertNull(
        $loader->load(
          NULL,
          'xdm-json'
        )
      );
    }

    public function testLoadWithInvalidJsonStringExpectingException() {
      $loader = new JsonAsXDM();
      $this->expectException(\UnexpectedValueException::class);
      $loader->load(
        '{foo}}',
        'xdm-json'
      );
    }

    public function testLoadStoppingAtMaxDepth() {
      $loader = new JsonAsXDM(0, 2);
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>
        <map xmlns="http://www.w3.org/2005/xpath-functions">
          <array key="foo"/>
        </map>',
        $loader
          ->load(json_encode(['foo' => [1, 2, 3]]), 'xdm-json')
          ->saveXML()
      );
    }

    public function testLoadWithEmptyArray() {
      $loader = new JsonAsXDM(0, 1);
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?>
         <array xmlns="http://www.w3.org/2005/xpath-functions"/>',
        $loader
          ->load('[]', 'xdm-json')
          ->saveXML()
      );
    }

    public function testLoadFragmentWithString() {
      $loader = new JsonAsXDM();
      $json = [
        'numbers' => [21, 42]
      ];
      $fragment = $loader->loadFragment(
        json_encode($json),
        'xdm-json'
      );
      $this->assertXmlStringEqualsXmlString(
        '<map xmlns="http://www.w3.org/2005/xpath-functions">
          <array key="numbers">
            <number>21</number>
            <number>42</number>
          </array>
        </map>',
        $fragment->ownerDocument->saveXml($fragment)
      );
    }

    public function testLoadFragmentWithUnsupportedTypeExpectingNull() {
      $loader = new JsonAsXDM();
      $this->assertNull($loader->loadFragment('', 'unknown'));
    }
  }
}
