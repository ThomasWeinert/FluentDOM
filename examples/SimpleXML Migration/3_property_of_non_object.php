<?php
require __DIR__.'/../../vendor/autoload.php';

/*
 * A problem with the object syntax is that it can result in error
 * messages if an expected node structure does not exists.
 */
$element = simplexml_load_string('<foo/>');
var_dump($element->some->other->element);
// PHP Notice:  Trying to get property of non-object in

/*
 * Using Xpath avoids the problem. If the location path returns
 * and empty list, the result of the string cast will be an
 * empty string.
 */
$document = FluentDOM::load('<foo/>');
var_dump($document('string(/root/some/other/element)'));
// string(0) ""

/*
 * You can use Xpath to validate if a node exists of course.
 */
$document = FluentDOM::load('<foo/>');
var_dump($document('count(/root/some/other/element) > 0'));
// bool(false)