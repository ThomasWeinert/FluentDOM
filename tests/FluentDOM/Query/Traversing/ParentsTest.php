<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class TraversingParentsTest extends TestCase {

    protected $_directory = __DIR__;
    /**
     * @group Traversing
     * @group TraversingFind
     * @covers FluentDOM\Query::parents
     * @covers FluentDOM\Query::expandAll
     */
    public function testParents() {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
      $this->assertInstanceOf('FluentDOM\\Query', $fd);
      $parents = $fd
        ->find('//b')
        ->parents()
        ->map(
          function($node) {
            return $node->tagName;
          }
        );
      $this->assertTrue(is_array($parents));
      $this->assertContains('span', $parents);
      $this->assertContains('p', $parents);
      $this->assertContains('div', $parents);
      $this->assertContains('body', $parents);
      $this->assertContains('html', $parents);
      $parents = implode(', ', $parents);
      $doc = $fd
        ->find('//b')
        ->append('<strong>'.htmlspecialchars($parents).'</strong>');
      $this->assertInstanceOf('FluentDOM\\Query', $doc);
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $doc);
    }
  }
}