<?php
require_once __DIR__.'/../../vendor/autoload.php';

/*
 * This examples shows the different methods of the Creator function object.
 */

$_ = FluentDOM::create();
$_->formatOutput = TRUE;

echo $_(
  // element node name
  'root',
  // nest for a child element
  $_(
    'element',
    // with text node child
    'text',
    // and some attributes
    ['attr' => 'value']
  ),
  $_(
    'traversable-mapping',
    // the each method allow you to iterate arrays or objects
    $_->each(
      // first argument is the traversable/array
      ['one', 'two'],
      // second a function, first argument is the current value, second the key
      // make sure to push the Creator function into it.
      function($text, $index) use ($_) {
        return $_('item', $text, ['index' => $index]);
      }
    )
  ),
  // other node types are created using methods
  $_(
    'node-types',
    $_(
      'cdata',
      // a cdata section
      $_->cdata(' cdata section')
    ),
    $_(
      'comment',
      // a comment
      $_->comment('comment text')
    ),
    $_(
      'processing-instruction',
      // processing instruction
      $_->pi('php', 'echo "Hello World!";')
    )
  )
);