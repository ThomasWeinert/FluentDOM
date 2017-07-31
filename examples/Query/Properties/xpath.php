<?php
require __DIR__.'/../../../vendor/autoload.php';

$fd = new FluentDOM\Nodes(
  '<message>Hello World!</message>'
);
echo $fd->xpath->evaluate('string(//message)');