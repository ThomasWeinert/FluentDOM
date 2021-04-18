<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query\Traversing {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once __DIR__.'/../../TestCase.php';

  class ParentsTest extends TestCase {

    protected $_directory = __DIR__;
    /**
     * @group Traversing
     * @group TraversingFind
     * @covers \FluentDOM\Query::parents
     */
    public function testParents(): void {
      $fd = $this->getQueryFixtureFromFunctionName(__FUNCTION__);
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
      $document = $fd
        ->find('//b')
        ->append('<strong>'.htmlspecialchars($parents).'</strong>');
      $this->assertFluentDOMQueryEqualsXMLFile(__FUNCTION__, $document);
    }
  }
}
