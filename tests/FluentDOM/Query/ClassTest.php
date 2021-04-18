<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Query {

  use FluentDOM\TestCase;

  require_once __DIR__.'/../TestCase.php';

  class ClassTest extends TestCase {
    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::hasClass
     */
    public function testHasClassExpectingTrue(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $this->assertTrue($fd->hasClass('test1'));
    }

    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::hasClass
     */
    public function testHasClassExpectingFalse(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $this->assertFalse($fd->hasClass('INVALID_CLASSNAME'));
    }

    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::toggleClass
     * @covers \FluentDOM\Query::changeClassString
     * @covers \FluentDOM\Query::addClass
     */
    public function testAddClass(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $fd->addClass('added');
      $this->assertTrue($fd->hasClass('added'));
    }

    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::toggleClass
     * @covers \FluentDOM\Query::changeClassString
     * @covers \FluentDOM\Query::removeClass
     */
    public function testRemoveClass(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $fd->removeClass('test2');
      $this->assertEquals('test1', $fd[0]->getAttribute('class'));
      $this->assertFalse($fd[1]->hasAttribute('class'));
    }

    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::toggleClass
     * @covers \FluentDOM\Query::changeClassString
     * @covers \FluentDOM\Query::removeClass
     */
    public function testRemoveClassWithEmptyString(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $fd->removeClass();
      $this->assertFalse($fd[0]->hasAttribute('class'));
      $this->assertFalse($fd[1]->hasAttribute('class'));
    }

    /**
     * @group Attributes
     * @group AttributesClasses
     * @dataProvider dataProviderToggleClass
     * @covers \FluentDOM\Query::toggleClass
     * @covers \FluentDOM\Query::changeClassString
     */
    public function testToggleClass(
      string $toggle, string $expectedOne, string $expectedTwo
    ): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $fd->toggleClass($toggle);
      $this->assertEquals($expectedOne, $fd[0]->getAttribute('class'));
      $this->assertEquals($expectedTwo, $fd[1]->getAttribute('class'));
      $this->assertEquals($toggle, $fd[2]->getAttribute('class'));
    }

    public function dataProviderToggleClass(): array {
      return [
        ['test1', 'test2', 'test2 test1'],
        ['test2 test4', 'test1 test4', 'test4']
      ];
    }

    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::toggleClass
     * @covers \FluentDOM\Query::changeClassString
     */
    public function testToggleClassWithCallback(): void {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $fd->toggleClass(
        function($node, $index, $class) {
          return $class.' test4';
        }
      );
      $this->assertEquals('test4', $fd[0]->getAttribute('class'));
      $this->assertEquals('test4', $fd[1]->getAttribute('class'));
    }
  }
}
