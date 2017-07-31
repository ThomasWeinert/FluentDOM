<?php
/**
* Example file for function 'removeData'
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$xml = <<<XML
<div data-role="page" data-hidden="true" data-options='{"name":"John"}'> </div>
XML;


echo "Example for function 'removeData':\n\n";
$fd = FluentDOM($xml)->find('//div');

$fd->removeData('options');

echo $fd;