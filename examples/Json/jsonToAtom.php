<?php
/**
* Loads the FluentDOM GitHUB timeline and output it as an Atom feed.
*/
require_once __DIR__.'/../../vendor/autoload.php';

$url = 'https://api.github.com/repos/FluentDOM/FluentDOM/commits?per_page=5';
$options = [
  'http' => [
    'method' => 'GET',
    'header' => "User-Agent: Awesome-Octocat-App\r\n"
  ]
];

$json = file_get_contents($url, NULL, stream_context_create($options));
$commits = FluentDOM::load($json, 'text/json')('/*/*');

$_ = FluentDOM::create();
$_->formatOutput = TRUE;
$_->registerNamespace('atom', 'http://www.w3.org/2005/Atom');

$atom = $_(
  'atom:feed',
  $_('atom:title', 'FluentDOM Commits'),
  $_->each(
    $commits,
    function(FluentDOM\DOM\Element $commit) use ($_) {
      return $_(
        'atom:entry',
        $_('atom:title', $commit('string(commit/committer/name)')),
        $_('atom:author',
          $_('atom:name', $commit('string(commit/committer/name)')),
          $_('atom:email', $commit('string(commit/committer/email)'))
        ),
        $_('atom:id', $commit('string(url)')),
        $_('atom:updated', $commit('string(commit/committer/date)')),
        $_(
          'atom:link',
          [
            'rel' => 'alternate',
            'type' => 'text/html',
            'href' => $commit('string(html_url)')
          ]
        ),
        $_(
          'atom:link',
          [
            'rel' => 'image',
            'type' => 'image/png',
            'href' => $commit('string(committer/avatar_url)')
          ]
        ),
        $_(
          'atom:summary',
           $commit('string(commit/message)'),
          [
            'type' => 'text'
          ]
        )
      );
    }
  )
)->document;

header('Content-type: application/atom+xml');
echo $atom->saveXml();


