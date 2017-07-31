<?php
require __DIR__.'/../../vendor/autoload.php';

$htmlFile = 'http://www.heise.de/';
$links = [];

$document = FluentDOM::load(
  $htmlFile,
  'text/html',
  [FluentDOM\Loader\Options::ALLOW_FILE => TRUE]
);
foreach ($document('//a[@href]') as $a) {
  $links[] = [
    'caption' => (string)$a,
    'href' => $a['href']
  ];
}

var_dump($links);