<?php

require_once(dirname(__FILE__).'/../../vendor/autoload.php');

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