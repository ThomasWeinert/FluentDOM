<?php
require_once(__DIR__.'/../../vendor/autoload.php');

$json = <<<JSON
{
  "firstName": "John"
}
JSON;

$fd = FluentDOM($json, 'text/json');
echo $fd->find('/*')->append('{"lastName": "Smith"}');


$fd = FluentDOM('{"firstName": "John"}', 'text/json');
$fd->contentType = 'text/xml';
echo $fd->find('/*')->append('<lastName>Smith</lastName>');