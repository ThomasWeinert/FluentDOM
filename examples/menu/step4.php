<?php
require('../../FluentDOM.php');

$fd = new FluentDOM();
$fd->contentType = 'html';

$menu = $fd->append('<ul/>');

$menu
  ->append('<li/>')
  ->append('<a/>')
  ->attr('href', '/sample.php')
  ->text('Sample');

$menu->append('<li/>')
  ->append('<a/>')
  ->attr('href', 'http://fluentdom.org')
  ->addClass('externalLink')
  ->text('FluentDOM');

$menu
  ->append('<li/>')
  ->append('<a/>')
  ->attr('id', 'alertSample')
  ->attr('onclick', "alert('Hi');")
  ->text('Alert Sample');

// mark first siblings
$fd
  // find all first <li>
  ->find('//li[1]')
  // set a class
  ->addClass('first');

// mark last siblings
$fd
  // find all last <li>
  ->find('//li[position() = last()]')
  // set a class
  ->addClass('last');

echo $fd;
?>