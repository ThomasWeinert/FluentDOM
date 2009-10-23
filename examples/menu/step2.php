<?php
require('../../FluentDOM.php');

// create a FluentDOM
$fd = new FluentDOM();
// we generate html
$fd->contentType = 'html';

//add the base menu node
$menu = $fd->append('<ul/>');

// add the first menu item
$menu
  // add the <li>
  ->append('<li/>')
  // add an <a> into the <li>
  ->append('<a/>')
  // set the href attribute of the <a>
  ->attr('href', '/sample.php')
  // set the text content of the <a>
  ->text('Sample');

// add the second menu item
$menu
  ->append('<li/>')
  ->append('<a/>')
  ->attr('href', 'http://fluentdom.org')
  ->text('FluentDOM');

// output the created document
echo $fd;
?>