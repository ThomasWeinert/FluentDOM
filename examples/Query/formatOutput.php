<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$xml = <<<XML
<nodes><one/><two/><three/></nodes>
XML;

echo FluentDOM($xml)->formatOutput();

