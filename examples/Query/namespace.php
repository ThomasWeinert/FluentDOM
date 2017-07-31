<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$fd = new FluentDOM\Nodes(
  '<foo:message xmlns:foo="urn:foo">Hello World!</foo:message>'
);
$fd->registerNamespace('bar', 'urn:foo');
echo $fd->find('//bar:message')[0];
