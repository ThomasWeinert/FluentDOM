
<?php
require(__DIR__.'/../../../vendor/autoload.php');

$_ = FluentDOM::create();
$_->formatOutput = TRUE;

$links = [
  'http://www.fluentdom.org' => 'FluentDOM',
  'http://www.php.net' => 'PHP'
];

echo $_(
  'ul',
  ['class' => 'navigation'],
  $_->each(
    $links,
    function($text, $href) use($_) {
      return $_('li', $_('a', ['href' => $href], $text));
    }
  )
)->document->saveHtml();