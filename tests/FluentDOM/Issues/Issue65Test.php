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

  class Issue65Test extends TestCase {

    public function testWithFind(): void {
      $fd = \FluentDOM::Query(
        '<p>Paragraph 1</p> <p>Paragraph 2</p><p>Paragraph 3</p><div><b>5</b><p>4</p></div>',
        'html-fragment'
      );
      $fd->find('/p')->first()->replaceWith('hi');

      $html = (string)$fd;
      $this->assertThat(
        $html,
        $this->logicalOr(
          "hi <p>Paragraph 2</p><p>Paragraph 3</p><div><b>5</b><p>4</p></div>",
          "hi <p>Paragraph 2</p><p>Paragraph 3</p><div>\n<b>5</b><p>4</p>\n</div>"
        )
      );
    }

    public function testWithFilter(): void {
      $fd = \FluentDOM::Query(
        '<p>Paragraph 1</p> <p>Paragraph 2</p><p>Paragraph 3</p><div><b>5</b><p>4</p></div>',
        'html-fragment'
      );
      $fd->filter('self::p')->first()->replaceWith('hi');

      $html = (string)$fd;
      $this->assertThat(
        $html,
        $this->logicalOr(
          "hi <p>Paragraph 2</p><p>Paragraph 3</p><div><b>5</b><p>4</p></div>",
          "hi <p>Paragraph 2</p><p>Paragraph 3</p><div>\n<b>5</b><p>4</p>\n</div>"
        )
      );
    }

  }
}

