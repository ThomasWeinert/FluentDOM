<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace FluentDOM {

  require_once __DIR__.'/../TestCase.php';

  class Issue20Test extends TestCase {

    public function testAppendElementInConstructor(): void {
      $message = new Issue20_Message();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?><message/>',
        (string)$message
      );
    }
  }

  class Issue20_Message extends Query {

    public function __construct(){
      parent::__construct();
      $this->append("<message/>");
    }
  }
}
