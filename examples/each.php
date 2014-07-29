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
echo $fd
  ->find('//item')
  ->each(
    function($node, $index) {
      $node['index'] = $index;
    }
  );
