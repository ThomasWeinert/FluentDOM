<?php
namespace FluentDOM\Query {

  use FluentDOM\Query;
  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class ManipulationTextTest extends TestCase {

    protected $_directory = __DIR__;

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::text
     */
    public function testTextRead() {
      $expect = 'text1text2text3';
      $text = $this->getQueryFixtureFromString(self::XML)->formatOutput()->find('//group')->text();
      $this->assertEquals($expect, $text);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::text
     */
    public function testTextWrite() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $textFd = $fd->text('changed');
      $this->assertEquals('changed', $fd[0]->textContent);
      $this->assertEquals('changed', $fd[1]->textContent);
      $this->assertTrue($fd === $textFd);
    }

    /**
     * @group Manipulation
     * @group ManipulationInside
     * @covers FluentDOM\Query::text
     */
    public function testTextWriteWithCallback() {
      $fd = $this->getQueryFixtureFromString(self::XML)->find('//item');
      $textFd = $fd->text(
        function ($node, $index, $text) {
          return 'Callback #'.$index.': '.$text;
        }
      );
      $this->assertEquals('Callback #0: text1', $fd[0]->textContent);
      $this->assertEquals('Callback #1: text2', $fd[1]->textContent);
      $this->assertTrue($fd === $textFd);
    }
  }
}