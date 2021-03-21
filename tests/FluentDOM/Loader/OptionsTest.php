<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\TestCase;
  use FluentDOM\Exceptions\InvalidSource;

  require_once __DIR__.'/../TestCase.php';

  class OptionsTest extends TestCase  {

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructor(): void {
      $options = new Options();
      $this->assertInstanceOf(Options::class, $options);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructorWithOptionsInArray(): void {
      $options = new Options([Options::IS_FILE => TRUE]);
      $this->assertTrue($options[Options::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructorWithOptionsInIterator(): void {
      $options = new Options(new \ArrayIterator([Options::IS_FILE => TRUE]));
      $this->assertTrue($options[Options::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructorWithOptionsExpectingException(): void {
      $this->expectException(InvalidArgument::class);
      new Options('No STRING ALLOWED');
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructorWithCallback(): void {
      $options = new Options(
        [],
        [
          Options::CB_IDENTIFY_STRING_SOURCE => function() { return FALSE; }
        ]
      );
      $this->assertEquals(Options::IS_FILE, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testConstructorWithCallbackExpectingException(): void {
      $this->expectException(\InvalidArgumentException::class);
      new Options([], ['UnknownCallback' => function() {} ]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testSetIsStringDisablesFileOptions(): void {
      $options = new Options(
        [ Options::ALLOW_FILE => TRUE, Options::IS_FILE => TRUE ]
      );
      $options[Options::IS_STRING] = TRUE;
      $this->assertTrue($options[Options::IS_STRING]);
      $this->assertFalse($options[Options::ALLOW_FILE]);
      $this->assertFalse($options[Options::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testSetIsFileDisablesStringOptionActivatesAllowFile(): void {
      $options = new Options(
        [ Options::ALLOW_FILE => FALSE, Options::IS_STRING => TRUE ]
      );
      $options[Options::IS_FILE] = TRUE;
      $this->assertFalse($options[Options::IS_STRING]);
      $this->assertTrue($options[Options::ALLOW_FILE]);
      $this->assertTrue($options[Options::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testSetDisallowFileDisablesFileOption(): void {
      $options = new Options(
        [ Options::ALLOW_FILE => TRUE, Options::IS_FILE => TRUE ]
      );
      $options[Options::ALLOW_FILE] = FALSE;
      $this->assertFalse($options[Options::ALLOW_FILE]);
      $this->assertFalse($options[Options::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testGetIterator(): void {
      $options = new Options(
        [ Options::ALLOW_FILE => TRUE, Options::IS_FILE => TRUE ]
      );
      $this->assertInstanceOf(\Traversable::class, $options);
      $this->assertEquals(
        [
          Options::PRESERVE_WHITESPACE => FALSE,
          Options::ALLOW_FILE => TRUE,
          Options::IS_FILE => TRUE,
          Options::IS_STRING => FALSE,
        ],
        iterator_to_array($options)
      );
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testForExistingOption(): void {
      $options = new Options(
        [ Options::ALLOW_FILE => TRUE ]
      );
      $this->assertTrue(isset($options[Options::ALLOW_FILE]));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testForNonExistingOption(): void {
      $options = new Options(
        [ Options::ALLOW_FILE => TRUE ]
      );
      $this->assertFalse(isset($options['UNKNOWN']));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testUnsetOption(): void {
      $options = new Options(
        [ Options::ALLOW_FILE => TRUE ]
      );
      unset($options[Options::ALLOW_FILE]);
      $this->assertNull($options[Options::ALLOW_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testGetSourceTypeWithoutIdentifyStringCallbackExpectingIsStringIsTrue(): void {
      $options = new Options();
      $this->assertEquals(Options::IS_STRING, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testGetSourceTypeExpectingIsStringIsTrue(): void {
      $options = new Options(
        [],
        [
          Options::CB_IDENTIFY_STRING_SOURCE => function() { return TRUE; }
        ]
      );
      $this->assertEquals(Options::IS_STRING, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testGetSourceTypeExpectingIsFileIsTrue(): void {
      $options = new Options(
        [],
        [
          Options::CB_IDENTIFY_STRING_SOURCE => function() { return FALSE; }
        ]
      );
      $this->assertEquals(Options::IS_FILE, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testGetSourceTypeForcingIsFile(): void {
      $options = new Options(
        [Options::IS_FILE => TRUE],
        [
          Options::CB_IDENTIFY_STRING_SOURCE => function() { return TRUE; }
        ]
      );
      $this->assertEquals(Options::IS_FILE, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testGetSourceTypeForcingIsString(): void {
      $options = new Options(
        [Options::IS_STRING => TRUE],
        [
          Options::CB_IDENTIFY_STRING_SOURCE => function() { return FALSE; }
        ]
      );
      $this->assertEquals(Options::IS_STRING, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testIsAllowedFileExpectingTrue(): void {
      $options = new Options(
        [Options::IS_FILE => TRUE]
      );
      $this->assertTrue($options->isAllowed(Options::IS_FILE));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testIsAllowedFileExpectingException(): void {
      $options = new Options(
        [Options::IS_FILE => FALSE]
      );
      $this->expectException(InvalidSource\TypeFile::class);
      $options->isAllowed(Options::IS_FILE);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testIsAllowedStringExpectingException(): void {
      $options = new Options(
        [Options::IS_FILE => TRUE]
      );
      $this->expectException(InvalidSource\TypeString::class);
      $options->isAllowed(Options::IS_STRING);
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testIsAllowedFileExpectingFalse(): void {
      $options = new Options(
        [Options::IS_FILE => FALSE]
      );
      $this->assertFalse($options->isAllowed(Options::IS_FILE, FALSE));
    }

    /**
     * @covers \FluentDOM\Loader\Options
     */
    public function testIsAllowedStringExpectingFalse(): void {
      $options = new Options(
        [Options::IS_FILE => TRUE]
      );
      $this->assertFalse($options->isAllowed(Options::IS_STRING, FALSE));
    }

  }
}


