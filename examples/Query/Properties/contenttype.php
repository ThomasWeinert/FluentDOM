<?php

require(__DIR__.'/../../../vendor/autoload.php');

$fd = new FluentDOM\Nodes(
  '<div>Hello World!<br/></div>', 'text/xml'
);
echo $fd, "\n";
$fd->contentType = 'text/html';
echo $fd;