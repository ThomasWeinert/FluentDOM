<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
header('Content-type: text/plain');

$xml = <<<XML
<nodes><one/><two/><three/></nodes>
XML;

require_once('../../vendor/autoload.php');

echo FluentDOM($xml)->formatOutput();

