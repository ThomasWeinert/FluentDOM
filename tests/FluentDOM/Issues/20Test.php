<?php
namespace FluentDOM {

  require_once(__DIR__.'/../TestCase.php');

  class Issue20Test extends TestCase {

    public function testAppendElementInConstructor() {
      $message = new Issue20_Message();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0" encoding="UTF-8"?><message/>',
        (string)$message
      );
    }
  }

  class Issue20_Message extends Query {

    function __construct(){
      $this->append("<message/>");
    }
  }
}