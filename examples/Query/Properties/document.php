<?php
require __DIR__.'/../../../vendor/autoload.php';

$fd = new FluentDOM\Query();
$fd->document->appendChild(
  $fd->document->createElement('example')
);
echo $fd;