<?php
require('../../src/_require.php');

// create a FluentDOM\Document
$dom = new FluentDOM\Document();

//add the base menu node
$menu = $dom->appendChild($dom->createElement('ul', ['class' => 'navigation']));

// add the first menu item
$menu
  ->appendElement('li')
  ->appendElement('a', 'Sample', ['href' => '/sample.php']);

// add the second menu item
$menu
  ->appendElement('li')
  ->appendElement('a', 'FluentDOM', ['href' => 'http://fluentdom.org']);

// output the created ul element as html
echo $dom->documentElement->saveHtml();
