<?php
namespace FluentDOM\Loader {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../TestCase.php');

  class OptionsTest extends TestCase  {

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructor() {
      $options = new Options();
      $this->assertInstanceOf(Options::class, $options);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructorWithOptionsInArray() {
      $options = new Options([Options::IS_FILE => TRUE]);
      $this->assertTrue($options[Options::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructorWithOptionsInIterator() {
      $options = new Options(new \ArrayIterator([Options::IS_FILE => TRUE]));
      $this->assertTrue($options[Options::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructorWithCallback() {
      $options = new Options(
        [],
        [
          Options::CB_IDENTIFY_STRING_SOURCE => function() { return FALSE; }
        ]
      );
      $this->assertEquals(Options::IS_FILE, $options->getSourceType(''));
    }

  }
}


