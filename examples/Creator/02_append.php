<?php
require(__DIR__.'/../../vendor/autoload.php');

/*
 * The Creator can be combined with the DOM methods
 *
 * The return value implements FluentDOM\Appendable, which is
 * accepted by
 */

$_ = FluentDOM::create();
$_->formatOutput = TRUE;

$document = new FluentDOM\Document();
$document
  ->appendElement(
    'root'
  )
  ->append(
    $_('element', ['attr' => 'value'])
  )
  ->append(
    $_->each(['text', ['abc' => 'bar']])
  );

echo $document->saveXML();