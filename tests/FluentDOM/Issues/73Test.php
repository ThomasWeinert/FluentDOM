<?php
namespace FluentDOM {

  use FluentDOM\Nodes\Creator;

  require_once(__DIR__.'/../TestCase.php');

  class Issue73Test extends TestCase {

    public function testCreatorDoesNotForgetNamespaces() {
      $_ = new Creator();
      $_->registerNamespace('atom', 'urn:atom');
      $result = $_(
        'atom:feed',
        $_->each(
          [1, 2, 3],
          function($number) use ($_) {
            return $_(
              'atom:entry',
              $_('atom:title', $number)
            );
          }
        )
      )->getDocument();
      $this->assertXmlStringEqualsXmlString(
        '<?xml version="1.0"?>
          <atom:feed xmlns:atom="urn:atom">
            <atom:entry>
              <atom:title>1</atom:title>
            </atom:entry>
            <atom:entry>
              <atom:title>2</atom:title>
            </atom:entry>
            <atom:entry>
              <atom:title>3</atom:title>
            </atom:entry>
          </atom:feed>',
        (string)$result->saveXML()
      );
    }
  }
}