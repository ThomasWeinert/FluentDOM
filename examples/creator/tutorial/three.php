<?php
require(__DIR__.'/../../../vendor/autoload.php');

$_ = FluentDOM::create();

echo $_(
  'ul',
  ['class' => 'navigation'],
  $_(
    'li',
    $_->cdata('FluentDOM')
  )
);