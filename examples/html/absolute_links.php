<?php
require('../../FluentDOM.php');

$url = 'http://fluentdom.org/';
$html = file_get_contents($url);

$fd = FluentDOM($html, 'html')->find('//a[@href]')->each(
  function ($node) use ($url) {
    $item = FluentDOM($node);
    if (!preg_match('(^[a-zA-Z]+://)', $item->attr('href'))) {
      $item->attr('href', $url.$item->attr('href'));
    }
  }
);
$fd->contentType = 'xml';

header('Content-type: text/xml');
echo $fd;

?>