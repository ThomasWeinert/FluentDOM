<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

require_once('../vendor/autoload.php');
$fd = new FluentDOM\Nodes(
  '<items><one/><two/><three/></items>'
);

$list = array_merge(
  $fd->find('/items/*')->toArray(),
  $fd->find('//*')->toArray()
);

var_dump(count($list), count($fd->unique($list)));