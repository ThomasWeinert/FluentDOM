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
echo $fd
  ->find('/items')
  ->find('item')
  ->each(
    function($node, $index) {
      $node['index'] = $index;
    }
  )
  ->end()
  ->each(
    function($node, $index) {
      $node['length'] = count($node);
    }
  );
