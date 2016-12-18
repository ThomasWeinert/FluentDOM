<?php
require(__DIR__.'/../../vendor/autoload.php');

$_ = FluentDOM::create();
$_->formatOutput = TRUE;
echo $_(
  'select',
  ['name' => 'example'],
  $_->each(
    ['One', 'Two', 'Three'],
    function($text, $index) use ($_) {
      return $_('option', ['value' => $index], $text);
    }
  )
)->document->saveHTML();