<?php
namespace FluentDOM\Nodes {

  use FluentDOM\Document;
  use FluentDOM\Exceptions;
  use FluentDOM\TestCase;
  use FluentDOM\Nodes;

  require_once(__DIR__.'/../TestCase.php');

  class BuilderTest extends TestCase {

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testConstructor() {
      $nodes = $this->getMockBuilder(Nodes::class)->getMock();
      $builder = new Builder($nodes);
      $this->assertSame(
        $nodes,
        $builder->getOwner()
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesFromElementNode() {
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
    public function testGetTargetNodesFromTextNode() {
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
    public function testGetTargetNodesFromNodes() {
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
    public function testGetTargetNodesFromCallbackReturningNodesAsArray() {
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
    public function testGetTargetNodesUsingSelector() {
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
    public function testGetTargetNodesUsingSelectorAndContext() {
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
    public function testGetTargetNodesUsingSelectorReturningScalarExpectingException() {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->expectException(
        \InvalidArgumentException::class,
        'Given selector did not return an node list'
      );
      $builder->getTargetNodes('count(//item)');
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetTargetNodesUsingInvalidSelectorExpectingException() {
      $nodes = new Nodes(self::XML);
      $builder = new Builder($nodes);
      $this->expectException(
        \InvalidArgumentException::class,
        'Invalid selector'
      );
      $builder->getTargetNodes(NULL);
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesWithElementNode() {
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
    public function testGetContentNodesWithTextNode() {
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
    public function testGetContentNodesWithTextNodeIgnoringTextNodes() {
      $nodes = new Nodes(self::XML);
      $node = $nodes->document->createTextNode("success");
      $builder = new Builder($nodes);
      $this->expectException(
        \FluentDOM\Exceptions\LoadingError::class
      );
      $this->assertSame(
        [$node],
        $builder->getContentNodes($node, FALSE)
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesWithLimit() {
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
    public function testGetContentNodesFromNodeListWithLimit() {
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
    public function testGetContentNodesWithXml() {
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
    public function testGetContentNodesWithHtml() {
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
    public function testGetContentNodesImportingNodes() {
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
    public function testGetContentNodesFromEmptyArrayExpectingException() {
      $document = new Document();
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->expectException(
        \FluentDOM\Exceptions\LoadingError::class
      );
      $builder->getContentNodes($document->xpath()->evaluate('//item'));
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentNodesFromEmptyStringExpectingException() {
      $document = new Document();
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->expectException(
        \FluentDOM\Exceptions\LoadingError::class
      );
      $builder->getContentNodes('');
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetContentElement() {
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
    public function testGetXmlFragment() {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertXmlNodesArrayEqualsXmlStrings(
        ['<one/>', '<two/>', 'three'],
        $builder->getFragment('<one/><two/>three', 'text/xml')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetXmlFragmentFromEmptyString() {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertEquals(
        [], $builder->getFragment('', 'text/xml')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetXmlFragmentWithInvalidFragment() {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->expectException(
        Exceptions\LoadingError\EmptySource::class
      );
      $builder->getFragment(NULL, 'text/xml');
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetXmlFragmentWithInvalidContentType() {
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
    public function testGetXmlFragmentWithInvalidFragmentBlockingErrors() {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertEquals(
        [],
        @$builder->getFragment('', 'text/xml')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetHtmlFragment() {
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
    public function testGetHtmlFragmentFromEmptyString() {
      $nodes = new Nodes();
      $builder = new Builder($nodes);
      $this->assertEquals(
        [], $builder->getFragment('', 'text/html')
      );
    }

    /**
     * @covers \FluentDOM\Nodes\Builder
     */
    public function testGetHtmlFragmentWithInvalidFragment() {
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
    public function testGetInnerXml() {
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
    public function testGetWrapperNodesSimple() {
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
    public function testGetWrapperNodesComplex() {
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

    public function assertXmlNodesArrayEqualsXmlStrings($expected, $nodes) {
      $actual = array();
      foreach ($nodes as $node) {
        $actual[] = $node->ownerDocument->saveXml($node);
      }
      $this->assertEquals($expected, $actual);
    }
  }
}