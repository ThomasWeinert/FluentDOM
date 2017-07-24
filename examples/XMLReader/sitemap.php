<?php

require(__DIR__.'/../../vendor/autoload.php');

$xml = <<<'XML'
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
    <url>
        <loc>http://www.example.com/videos/some_video_landing_page.html</loc>
        <video:video>
            <video:title>Grilling steaks for summer</video:title>
        </video:video>
    </url>
    <url>
        <loc>http://www.example.com/videos/another_video_landing_page.html</loc>
        <video:video>
            <video:title>Smoking meat for winter</video:title>
        </video:video>
    </url>
</urlset>
XML;

$reader = new FluentDOM\XMLReader();
$reader->open('data://text/plain;base64,'.base64_encode($xml));
$reader->registerNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$reader->registerNamespace('v', 'http://www.google.com/schemas/sitemap-video/1.1');

if ($reader->read('s:url')) {
  do {
    /** @var \FluentDOM\DOM\Element $url */
    $url = $reader->expand();
    var_dump(
      [
        $url('string(v:video/v:title)'),
        $url('string(s:loc)')
      ]
    );
  } while ($reader->next('s:url'));
}

