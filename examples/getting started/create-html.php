<?php
require(__DIR__.'/../../vendor/autoload.php');


$_ = FluentDOM::create();
$_->formatOutput = TRUE;
echo $_(
  'ul',
  $_->each(
    ['One', 'Two', 'Three'],
    function($text) use ($_) {
      return $_('li', $text);
    }
  )
);