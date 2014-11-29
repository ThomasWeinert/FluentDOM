<?php
/**
* Example file for function 'removeData'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2011 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<div data-role="page" data-hidden="true" data-options='{"name":"John"}'> </div>
XML;


echo "Example for function 'removeData':\n\n";
require_once('../vendor/autoload.php');
$fd = FluentDOM($xml)->find('//div');

$fd->removeData('options');

echo $fd;