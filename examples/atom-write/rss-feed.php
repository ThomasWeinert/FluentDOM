<?php

require_once(__DIR__.'/../../vendor/autoload.php');


$dom = new FluentDOM\Document();
$dom->registerNamespace('atom', 'http://www.w3.org/2005/Atom');

$rss = $dom->appendElement(
  'RSS',
  [
    'version' => '2.0',
    'xmlns:atom' => 'http://www.w3.org/2005/Atom'
  ]
);
$channel = $rss->appendElement('channel');
$channel->appendElement(
  'atom:link',
  [
    'rel' => 'next',
    'href' => 'http://domain.tld/feed.php?offset=10'
  ]
);

echo $dom->saveXml();