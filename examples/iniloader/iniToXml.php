<?php
/**
* Sample how to use a custom FluentDOM loader
*/

require_once(__DIR__.'/../../src/_require.php');
require_once(__DIR__.'/IniLoader.php');

$iniFile = __DIR__.'/sample.ini';

$fd = new FluentDOM\Query();
$fd->loaders(new IniLoader());

header('Content-type: text/plain');
echo $fd->load($iniFile, 'text/ini')->formatOutput();

echo "\n\n";
echo 'URL: ', $fd->find('//URL')->text();

