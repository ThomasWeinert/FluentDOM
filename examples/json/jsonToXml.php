<?php
/**
* Sample how to use the JSON loader
*
* It loads the FluentDOM github timeline.
*/

require_once(dirname(__FILE__).'/../../src/_require.php');

// get the loader object
$jsonLoader = new FluentDOM\Loader\JsonString();
// activate type attributes
$jsonLoader->typeAttributes = TRUE;
// get a FluentDOM
$fd = new FluentDOM\Query();
// inject the loader object
$fd->loaders($jsonLoader);

$url = 'https://api.github.com/repos/FluentDOM/FluentDOM/commits?per_page=5';
$options = array(
  'http'=>array(
    'method' => "GET",
    'header' => "User-Agent: Awesome-Octocat-App\r\n"
  )
);

$json = file_get_contents($url, NULL, stream_context_create($options));

header('Content-type: text/xml');
echo $fd->load($json, "json")->formatOutput();

