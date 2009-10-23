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

// remember the <li> so we cann add a submenu
$item = $menu
  ->append('<li/>')
  ->append('<a/>')
  ->attr('href', 'http://fluentdom.org')
  ->addClass('externalLink')
  ->text('FluentDOM')
  // <a> is selected, jump to parent selection
  ->end();

// add the submenu
$subMenu = $item->append('<ul/>');

// add the submenu item
$subMenu
  ->append('<li/>')
  ->append('<a/>')
  ->attr('href', 'http://nightly.fluentdom.org/documentation')
  ->addClass('externalLink')
  ->text('Documentation');

$menu
  ->append('<li/>')
  ->append('<a/>')
  ->attr('id', 'alertSample')
  ->attr('onclick', "alert('Hi');")
  ->text('Alert Sample');

$fd
  ->find('//li[1]')
  ->addClass('first');
$fd
  ->find('//li[position() = last()]')
  ->addClass('last');

echo $fd;
?>