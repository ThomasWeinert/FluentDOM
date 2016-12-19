<?php
require(__DIR__.'/../../../vendor/autoload.php');

$_ = FluentDOM::create();

echo $_(
  'ul',
  ['class' => 'navigation'],
  $_(
    'li',
    $_('a', ['href' => 'http://fluentdom.org'], 'FluentDOM')
  )
)->document->saveHtml();