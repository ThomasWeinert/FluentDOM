<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM\Loader {

  use FluentDOM\Exceptions\InvalidArgument;
  use FluentDOM\TestCase;
  use FluentDOM\Exceptions\InvalidSource;

  require_once __DIR__.'/../TestCase.php';

  class LoaderOptionsTest extends TestCase  {

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testConstructor(): void {
      $options = new LoaderOptions();
      $this->assertInstanceOf(LoaderOptions::class, $options);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testConstructorWithOptionsInArray(): void {
      $options = new LoaderOptions([LoaderOptions::IS_FILE => TRUE]);
      $this->assertTrue($options[LoaderOptions::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testConstructorWithOptionsInIterator(): void {
      $options = new LoaderOptions(new \ArrayIterator([LoaderOptions::IS_FILE => TRUE]));
      $this->assertTrue($options[LoaderOptions::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testConstructorWithCallback(): void {
      $options = new LoaderOptions(
        [],
        [
          LoaderOptions::CB_IDENTIFY_STRING_SOURCE => function() { return FALSE; }
        ]
      );
      $this->assertEquals(LoaderOptions::IS_FILE, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testConstructorWithCallbackExpectingException(): void {
      $this->expectException(\InvalidArgumentException::class);
      new LoaderOptions([], ['UnknownCallback' => static function() {} ]);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testSetIsStringDisablesFileOptions(): void {
      $options = new LoaderOptions(
        [ LoaderOptions::ALLOW_FILE => TRUE, LoaderOptions::IS_FILE => TRUE ]
      );
      $options[LoaderOptions::IS_STRING] = TRUE;
      $this->assertTrue($options[LoaderOptions::IS_STRING]);
      $this->assertFalse($options[LoaderOptions::ALLOW_FILE]);
      $this->assertFalse($options[LoaderOptions::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testSetIsFileDisablesStringOptionActivatesAllowFile(): void {
      $options = new LoaderOptions(
        [ LoaderOptions::ALLOW_FILE => FALSE, LoaderOptions::IS_STRING => TRUE ]
      );
      $options[LoaderOptions::IS_FILE] = TRUE;
      $this->assertFalse($options[LoaderOptions::IS_STRING]);
      $this->assertTrue($options[LoaderOptions::ALLOW_FILE]);
      $this->assertTrue($options[LoaderOptions::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testSetDisallowFileDisablesFileOption(): void {
      $options = new LoaderOptions(
        [ LoaderOptions::ALLOW_FILE => TRUE, LoaderOptions::IS_FILE => TRUE ]
      );
      $options[LoaderOptions::ALLOW_FILE] = FALSE;
      $this->assertFalse($options[LoaderOptions::ALLOW_FILE]);
      $this->assertFalse($options[LoaderOptions::IS_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testGetIterator(): void {
      $options = new LoaderOptions(
        [ LoaderOptions::ALLOW_FILE => TRUE, LoaderOptions::IS_FILE => TRUE ]
      );
      $this->assertInstanceOf(\Traversable::class, $options);
      $this->assertEquals(
        [
          LoaderOptions::PRESERVE_WHITESPACE => FALSE,
          LoaderOptions::ALLOW_FILE => TRUE,
          LoaderOptions::IS_FILE => TRUE,
          LoaderOptions::IS_STRING => FALSE,
        ],
        iterator_to_array($options)
      );
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testForExistingOption(): void {
      $options = new LoaderOptions(
        [ LoaderOptions::ALLOW_FILE => TRUE ]
      );
      $this->assertTrue(isset($options[LoaderOptions::ALLOW_FILE]));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testForNonExistingOption(): void {
      $options = new LoaderOptions(
        [ LoaderOptions::ALLOW_FILE => TRUE ]
      );
      $this->assertFalse(isset($options['UNKNOWN']));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testUnsetOption(): void {
      $options = new LoaderOptions(
        [ LoaderOptions::ALLOW_FILE => TRUE ]
      );
      unset($options[LoaderOptions::ALLOW_FILE]);
      $this->assertNull($options[LoaderOptions::ALLOW_FILE]);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testGetSourceTypeWithoutIdentifyStringCallbackExpectingIsStringIsTrue(): void {
      $options = new LoaderOptions();
      $this->assertEquals(LoaderOptions::IS_STRING, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testGetSourceTypeExpectingIsStringIsTrue(): void {
      $options = new LoaderOptions(
        [],
        [
          LoaderOptions::CB_IDENTIFY_STRING_SOURCE => function() { return TRUE; }
        ]
      );
      $this->assertEquals(LoaderOptions::IS_STRING, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testGetSourceTypeExpectingIsFileIsTrue(): void {
      $options = new LoaderOptions(
        [],
        [
          LoaderOptions::CB_IDENTIFY_STRING_SOURCE => function() { return FALSE; }
        ]
      );
      $this->assertEquals(LoaderOptions::IS_FILE, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testGetSourceTypeForcingIsFile(): void {
      $options = new LoaderOptions(
        [LoaderOptions::IS_FILE => TRUE],
        [
          LoaderOptions::CB_IDENTIFY_STRING_SOURCE => function() { return TRUE; }
        ]
      );
      $this->assertEquals(LoaderOptions::IS_FILE, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testGetSourceTypeForcingIsString(): void {
      $options = new LoaderOptions(
        [LoaderOptions::IS_STRING => TRUE],
        [
          LoaderOptions::CB_IDENTIFY_STRING_SOURCE => function() { return FALSE; }
        ]
      );
      $this->assertEquals(LoaderOptions::IS_STRING, $options->getSourceType(''));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testIsAllowedFileExpectingTrue(): void {
      $options = new LoaderOptions(
        [LoaderOptions::IS_FILE => TRUE]
      );
      $this->assertTrue($options->isAllowed(LoaderOptions::IS_FILE));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testIsAllowedFileExpectingException(): void {
      $options = new LoaderOptions(
        [LoaderOptions::IS_FILE => FALSE]
      );
      $this->expectException(InvalidSource\TypeFile::class);
      $options->isAllowed(LoaderOptions::IS_FILE);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testIsAllowedStringExpectingException(): void {
      $options = new LoaderOptions(
        [LoaderOptions::IS_FILE => TRUE]
      );
      $this->expectException(InvalidSource\TypeString::class);
      $options->isAllowed(LoaderOptions::IS_STRING);
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testIsAllowedFileExpectingFalse(): void {
      $options = new LoaderOptions(
        [LoaderOptions::IS_FILE => FALSE]
      );
      $this->assertFalse($options->isAllowed(LoaderOptions::IS_FILE, FALSE));
    }

    /**
     * @covers \FluentDOM\Loader\LoaderOptions
     */
    public function testIsAllowedStringExpectingFalse(): void {
      $options = new LoaderOptions(
        [LoaderOptions::IS_FILE => TRUE]
      );
      $this->assertFalse($options->isAllowed(LoaderOptions::IS_STRING, FALSE));
    }

  }
}


