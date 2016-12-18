<?php
/**
* Sample how to use a custom FluentDOM loader
*/

require_once(__DIR__.'/../../vendor/autoload.php');
require_once(__DIR__.'/IniLoader.php');

header('Content-type: text/plain');
$iniFile = __DIR__.'/sample.ini';

/*
 * Register the loader on the static class
 */
FluentDOM::registerLoader(new IniLoader());

/*
 * You can use it with the Query Api
 */
$fd = FluentDOM($iniFile, 'text/ini');
echo 'URL: ', $fd->find('//URL')->text(), "\n\n";

/*
 * Or as a DOM document
 */
$document = FluentDOM::load($iniFile, 'text/ini');
echo 'URL: ', $document('string(//URL)'), "\n\n";

$document->formatOutput = TRUE;
echo $document->saveXml();

