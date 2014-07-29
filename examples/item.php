<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

require_once('../vendor/autoload.php');
$fd = new FluentDOM\Nodes(
  '<items><item/><item/><item/></items>'
);
$fd = $fd->find('//item');

$length = count($fd);
for ($i = 0; $i < $length; $i++) {
  $fd->item($i)->setAttribute('index', $i);
}

echo $fd;
