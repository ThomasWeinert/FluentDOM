<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$fd = new FluentDOM\Nodes(
  '<items><one/><two/><three/></items>'
);

$list = array_merge(
  $fd->find('/items/*')->toArray(),
  $fd->find('//*')->toArray()
);

var_dump(count($list), count($fd->unique($list)));