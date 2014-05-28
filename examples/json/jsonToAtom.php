<?php
/**
* Loads the FluentDOM github timeline and output it as an Atom feed.
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
$commits = FluentDOM($json, "text/json")->find('/*/*');

$atom = new FluentDOM\Document();
$atom->formatOutput = TRUE;
$atom->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
$feed = $atom->appendElement('atom:feed');
$feed->appendElement('atom:title', 'FluentDOM Commits');
foreach ($commits as $commit) {
  $entry = $feed->appendElement('atom:entry');
  $entry->appendElement('atom:title', $commit->evaluate('string(commit/committer/name)'));
  $author = $entry->appendElement('atom:author');
  $author->appendElement('atom:name', $commit->evaluate('string(commit/committer/name)'));
  $author->appendElement('atom:email', $commit->evaluate('string(commit/committer/email)'));
  $entry->appendElement('atom:id', $commit->evaluate('string(url)'));
  $entry->appendElement('atom:updated', $commit->evaluate('string(commit/committer/date)'));
  $entry->appendElement(
    'atom:link',
    [
      'rel' => 'alternate',
      'type' => 'text/html',
      'href' => $commit->evaluate('string(html_url)')
    ]
  );
  $entry->appendElement(
    'atom:summary',
    $commit->evaluate('string(commit/message)'),
    [
      'type' => 'text'
    ]
  );
  $entry->appendElement(
    'atom:link',
    [
      'rel' => 'image',
      'type' => 'image/png',
      'href' => $commit->evaluate('string(committer/avatar_url)')
    ]
  );
}

header('Content-type: application/atom+xml');
echo $atom->saveXml();


