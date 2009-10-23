<?php
require('../../FluentDOM.php');

// create a FluentDOM
$fd = new FluentDOM();
// we generate html
$fd->contentType = 'html';

//add the base menu node
$menu = $fd->append(
  //create a dom element node
  $fd->document->createElement('ul')
);

// output the created document
echo $fd;
?>