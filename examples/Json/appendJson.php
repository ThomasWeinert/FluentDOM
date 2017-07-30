<?php
require_once __DIR__.'/../../vendor/autoload.php';

/*
 * FluentDOM\Query uses the same loader for fragments.
 * So if you loaded json, methods like append, expect a
 * json fragment.
 */
$fd = FluentDOM($json, 'text/json');
echo $fd->find('/*')->append('{"lastName": "Smith"}');

/*
 * You can change the content type to switch the loader.
 */
$fd = FluentDOM('{"firstName": "John"}', 'text/json');
$fd->contentType = 'text/xml';
echo $fd->find('/*')->append('<lastName>Smith</lastName>');