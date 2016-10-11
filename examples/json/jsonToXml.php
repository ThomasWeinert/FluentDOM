<?php
/**
* Sample how to use the JSON loader
*
* It loads the FluentDOM github timeline.
*/

require_once(dirname(__FILE__).'/../../vendor/autoload.php');

$url = 'https://api.github.com/repos/FluentDOM/FluentDOM/commits?per_page=5';
$options = array(
  'http'=>array(
    'method' => "GET",
    'header' => "User-Agent: Awesome-Octocat-App\r\n"
  )
);

$json = file_get_contents($url, NULL, stream_context_create($options));

header('Content-type: text/xml');
echo FluentDOM($json, "text/json")->formatOutput('text/xml');

