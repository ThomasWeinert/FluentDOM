<?php

require_once(__DIR__.'/../../vendor/autoload.php');

/*
 * You can use a generator to append multiple nodes also.
 */

$_ = FluentDOM::create();
$_->formatOutput = TRUE;

echo $_(
  'root',
  function() use ($_) {
    for ($i = 0; $i < 2; $i++) {
      yield $_('item', (string)$i);
    }
  }
);