<?php

require_once(dirname(__FILE__).'/../../vendor/autoload.php');

$_ = FluentDOM::create();
$_->formatOutput = TRUE;

echo $_(
  'root',
  $_('element', 'text', ['attr' => 'value']),
  $_(
    'traversable-mapping',
    $_->each(
      ['one', 'two'],
      function($text, $index) use ($_) {
        return $_('item', $text, ['index' => $index]);
      }
    )
  ),
  $_(
    'node-types',
    $_(
      'cdata',
      $_->cdata(' cdata section')
    ),
    $_(
      'comment',
      $_->comment('comment text')
    ),
    $_(
      'processing-instruction',
      $_->pi('php', 'echo "Hello World!";')
    )
  )
);