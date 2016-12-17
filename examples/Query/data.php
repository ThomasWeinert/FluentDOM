<?php
/**
* Example file for function 'data'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2011-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');
require_once('../../vendor/autoload.php');

$xml = <<<XML
<div data-role="page" data-hidden="true" data-options='{"name":"John"}'> </div>
XML;

echo "Example for function 'data':\n\n";
$fd = FluentDOM($xml)->find('//div');

var_dump($fd->data('role'));
var_dump($fd->data('hidden'));
var_dump($fd->data('options')->name);