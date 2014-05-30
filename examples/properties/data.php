<?php
/**
* Example file for property 'data'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2011 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<div data-role="page" data-hidden="true" data-options='{"name":"John"}'> </div>
XML;


echo "Example for property 'data':\n";
require_once('../../vendor/autoload.php');
$fd = FluentDOM($xml)->find('//div');

echo "\nRead:\n";
var_dump($fd->data->role);
var_dump($fd->data->hidden);
var_dump($fd->data->options->name);

echo "\nWrite:\n";
$options = $fd->data->options;
$options->lastName = 'Doe';
$fd->data->options = $options;
var_dump($fd->data->options);

echo "\nIsset/Unset:\n";
var_dump(isset($fd->data->hidden));
unset($fd->data->hidden);
var_dump(isset($fd->data->hidden));

echo "\nChanged XML:\n";
echo $fd;

