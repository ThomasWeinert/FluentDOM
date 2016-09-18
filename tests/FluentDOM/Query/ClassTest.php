<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class ClassTest extends TestCase {
    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::hasClass
     */
    public function testHasClassExpectingTrue() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $this->assertTrue($fd->hasClass('test1'));
    }

    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::hasClass
     */
    public function testHasClassExpectingFalse() {
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
    public function testAddClass() {
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
    public function testRemoveClass() {
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
    public function testRemoveClassWithEmptyString() {
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
    public function testToggleClass($toggle, $expectedOne, $expectedTwo) {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//html/div');
      $fd->toggleClass($toggle);
      $this->assertEquals($expectedOne, $fd[0]->getAttribute('class'));
      $this->assertEquals($expectedTwo, $fd[1]->getAttribute('class'));
      $this->assertEquals($toggle, $fd[2]->getAttribute('class'));
    }

    public function dataProviderToggleClass() {
      return array(
        array('test1', 'test2', 'test2 test1'),
        array('test2 test4', 'test1 test4', 'test4')
      );
    }

    /**
     * @group Attributes
     * @group AttributesClasses
     * @covers \FluentDOM\Query::toggleClass
     * @covers \FluentDOM\Query::changeClassString
     */
    public function testToogleClassWithCallback() {
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