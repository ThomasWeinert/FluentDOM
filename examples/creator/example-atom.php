<?php

require('../../vendor/autoload.php');

$_ = FluentDOM::create();
$_->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
$_->formatOutput = TRUE;

echo $_(
  'atom:feed',
  $_('atom:title', 'Example Feed'),
  $_('atom:link', ['href' => 'http://example.org/']),
  $_('atom:updated', '2003-12-13T18:30:02Z'),
  $_(
    'atom:author',
    $_('atom:name', 'John Doe')
  ),
  $_('atom:id', 'urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6'),
  $_(
    'atom:entry',
    $_('atom:title', 'Atom-Powered Robots Run Amok'),
    $_('atom:link', ['href' => 'http://example.org/2003/12/13/atom03']),
    $_('atom:id', 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a'),
    $_('atom:updated', '2003-12-13T18:30:02Z'),
    $_('atom:summary', 'Some text.')
  )
);
