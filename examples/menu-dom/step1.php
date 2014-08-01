<?php
require('../../src/_require.php');

// create a FluentDOM\Document
$dom = new FluentDOM\Document();

//add the base menu node
$menu = $dom->appendChild($dom->createElement('ul', ['class' => 'navigation']));

// step 2

// output the created ul element as html
echo $dom->documentElement->saveHtml();
