<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$fd = new FluentDOM\Nodes(
  '<items><item/><item/><item/></items>'
);
$fd = $fd->find('//item');

$length = count($fd);
for ($i = 0; $i < $length; $i++) {
  $fd->item($i)->setAttribute('index', $i);
}

echo $fd;
