<?php
require(dirname(__FILE__).'/../../vendor/autoload.php');

$_ = FluentDOM::create();
$_->formatOutput = TRUE;

$dom = new FluentDOM\Document();
$dom
  ->appendElement(
    'root'
  )
  ->append(
    $_('element', ['attr' => 'value'])
  )
  ->append(
    $_->any(['text', ['abc' => 'bar']])
  );

echo $dom->saveXML();