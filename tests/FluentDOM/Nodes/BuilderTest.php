<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Nodes {

  use FluentDOM\DOM\Document;
  use FluentDOM\Exceptions;
  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\TestCase;
  use FluentDOM\Nodes;

  require_once __DIR__.'/../TestCase.php';

  class BuilderTest extends TestCase {

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testConstructor(): void {
      $nodes = $this->createMock(Nodes::class);
      $builder = new Builder($nodes);
      $this->assertSame(
        $nodes,
        $builder->getOwner()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesFromElementNode(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->assertSame(
        [$nodes->document->documentElement],
        $builder->getTargetNodes($nodes->document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesFromTextNode(): void {
      $nodes = new Nodes();
      $node = $nodes->document->createTextNode('success');
      $builder = new Builder($nodes);
      $this->assertSame(
        [$node],
        $builder->getTargetNodes($node)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesFromNodes(): void {
      $nodes = (new Nodes(self::XML))->find('/*');
      $builder = new Builder($nodes);
      $this->assertSame(
        [$nodes->document->documentElement],
        $builder->getTargetNodes($nodes)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesFromCallbackReturningNodesAsArray(): void {
      $nodes = (new Nodes(self::XML))->find('/*');
      $builder = new Builder($nodes);
      $this->assertSame(
        [$nodes->document->documentElement],
        $builder->getTargetNodes(
          function() use ($nodes) {
            return $nodes->toArray();
          }
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesUsingSelector(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->assertSame(
        $nodes->find('//item')->toArray(),
        $builder->getTargetNodes('//item')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesUsingSelectorAndContext(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->assertSame(
        $nodes->find('//item')->toArray(),
        $builder->getTargetNodes('group/item', $nodes->document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesUsingSelectorReturningScalarExpectingException(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->expectException(\InvalidArgumentException::class);
      $this->expectExceptionMessage('Given selector did not return an node list');
      $builder->getTargetNodes('count(//item)');
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesUsingInvalidSelectorExpectingException(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->expectException(\InvalidArgumentException::class);
      $this->expectExceptionMessage('Invalid selector');
      $builder->getTargetNodes(NULL);
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesWithElementNode(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->assertSame(
        [$nodes->document->documentElement],
        $builder->getContentNodes($nodes->document->documentElement)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesWithTextNode(): void {
      $nodes = new Nodes(self::XML);
      $node = $nodes->document->createTextNode("success");
      $builder = new Builder($nodes);
      $this->assertSame(
        [$node],
        $builder->getContentNodes($node)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesWithTextNodeIgnoringTextNodes(): void {
      $nodes = new Nodes(self::XML);
      $node = $nodes->document->createTextNode("success");
      $builder = new Builder($nodes);
      $this->expectException(
        LoadingError::class
      );
      $this->assertSame(
        [$node],
        $builder->getContentNodes($node, FALSE)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesWithLimit(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $array = $nodes->find('//item')->toArray();
      $this->assertSame(
        [$array[0]],
        $builder->getContentNodes($array, TRUE, 1)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesFromNodeListWithLimit(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $array = $nodes->find('//item')->toArray();
      $this->assertSame(
        [$array[0]],
        $builder->getContentNodes(
          $nodes->xpath()->evaluate('//item'), TRUE, 1
        )
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesWithXml(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $array = $builder->getContentNodes('<test/>');
      $nodes->document->appendChild(
        $array[0]
      );
      $this->assertXmlStringEqualsXmlString(
        '<test/>',
        (string)$nodes
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesWithHtml(): void {
      $nodes = new Nodes();
      $nodes->contentType = 'text/html';
      $builder = new Builder($nodes);
      $array = $builder->getContentNodes('<input>');
      $nodes->document->appendChild(
        $array[0]
      );
      $this->assertEquals(
        "<input>\n",
        (string)$nodes
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesImportingNodes(): void {
      $document = new Document();
      $document->loadXml(self::XML);
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $array = $builder->getContentNodes($document->xpath()->evaluate('//item'), TRUE, 1);
      $nodes->document->appendChild($array[0]);
      $this->assertXmlStringEqualsXmlString(
        '<item index="0">text1</item>',
        (string)$nodes
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesFromEmptyArrayExpectingException(): void {
      $document = new Document();
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->expectException(
        LoadingError::class
      );
      $builder->getContentNodes($document->xpath()->evaluate('//item'));
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesFromEmptyStringExpectingException(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->expectException(
        LoadingError::class
      );
      $builder->getContentNodes('');
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentElement(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $node = $builder->getContentElement('<test/>');
      $nodes->document->appendChild($node);
      $this->assertXmlStringEqualsXmlString(
        '<test/>',
        (string)$nodes
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetXmlFragment(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<one/>', '<two/>', 'three'],
        $builder->getFragment('<one/><two/>three')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetXmlFragmentFromEmptyString(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertEquals(
        [], $builder->getFragment('')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetXmlFragmentWithInvalidFragment(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->expectException(
        Exceptions\LoadingError\EmptySource::class
      );
      $builder->getFragment(NULL);
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetXmlFragmentWithInvalidContentType(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->expectException(
        Exceptions\InvalidFragmentLoader::class
      );
      $builder->getFragment('', 'invalid');
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetXmlFragmentWithInvalidFragmentBlockingErrors(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertEquals(
        [],
        @$builder->getFragment('')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetHtmlFragment(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<br/>', 'TEXT', '<br/>'],
        $builder->getFragment('<br/>TEXT<br/>', 'text/html')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetHtmlFragmentFromEmptyString(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertEquals(
        [], $builder->getFragment('', 'text/html')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetHtmlFragmentWithInvalidFragment(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->expectException(
        Exceptions\LoadingError\EmptySource::class
      );
      $builder->getFragment(NULL, 'text/html');
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetInnerXml(): void {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->assertEquals(
        '<item index="0">text1</item><item index="1">text2</item><item index="2">text3</item>',
        $builder->getInnerXml($nodes->xpath()->evaluate('//group')->item(0))
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetWrapperNodesSimple(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $template = $builder->getContentElement('<simple/>');
      $simple = FALSE;
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<simple/>', '<simple/>'],
        $builder->getWrapperNodes($template, $simple)
      );
      $this->assertTrue($simple);
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetWrapperNodesComplex(): void {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $template = $builder->getContentElement(
        '<outer><between><inner/></between></outer>'
      );
      $simple = FALSE;
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<inner/>', '<outer><between><inner/></between></outer>'],
        $builder->getWrapperNodes($template, $simple)
      );
      $this->assertFalse($simple);
    }

    /**
     * @param array $expected
     * @param iterable $nodes
     */
    public function assertXmlNodesArrayEqualsXmlStrings(array $expected, iterable $nodes): void {
      $actual = [];
      foreach ($nodes as $node) {
        $actual[] = $node->ownerDocument->saveXml($node);
      }
      $this->assertEquals($expected, $actual);
    }
  }
}
