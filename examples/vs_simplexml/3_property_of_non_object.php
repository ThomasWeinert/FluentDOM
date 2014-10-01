<?php
require(dirname(__FILE__).'/../../vendor/autoload.php');

$element = simplexml_load_string('<foo/>');
var_dump($element->some->other->element);

$document = FluentDOM::load('<foo/>');
var_dump($document('string(/root/some/other/element)'));