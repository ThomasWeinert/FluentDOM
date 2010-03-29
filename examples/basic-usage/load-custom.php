<?php

require('../../FluentDOM.php');

$xmlFile = 'hello.xml';

class ExampleLoader implements FluentDOMLoader {

  // this could implement checks, error handling, ...
  public function load($source, $type) {
    $dom = new DOMDocument();
    $dom->load($source);
    return $dom;
  }
}

$fd = new FluentDOM();
// set loader(s)
$fd->setLoaders(
  array(
    new ExampleLoader
  )
);
// load data  using custom loader
$fd->load($xmlFile);

echo $fd
  ->find('/message')
  ->text('Hello World!');