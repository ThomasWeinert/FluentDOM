<?php
require('../../FluentDOM.php');

$fd = new FluentDOM();
$fd->contentType = 'html';

$menu = $fd->append('<ul/>');

// first menu item
$menu
  ->append('<li/>')
  ->append('<a/>')
  ->attr('href', '/sample.php')
  ->text('Sample');

// second menu item
$menu
  ->append('<li/>')
  ->append('<a/>')
  ->attr('href', 'http://fluentdom.org')
  // add a class to the menu item <a>
  ->addClass('externalLink')
  ->text('FluentDOM');

// third menu item
$menu
  ->append('<li/>')
  ->append('<a/>')
  // set the id attribute
  ->attr('id', 'alertSample')
  // set the js ionclick handler
  ->attr('onclick', "alert('Hi');")
  ->text('Alert Sample');

echo $fd;
?>