<?php
require __DIR__.'/../../vendor/autoload.php';

$videos = [
  [
    'url' => 'http://www.example.com/videos/some_video_landing_page.html',
    'title' => 'Grilling steaks for summer'
  ],
  [
    'url' => 'http://www.example.com/videos/another_video_landing_page.htm',
    'title' => 'Smoking meat for winter'
  ]
];

$writer = new FluentDOM\XMLWriter();
$writer->openURI('php://stdout');
$writer->registerNamespace('', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$writer->registerNamespace('video', 'http://www.google.com/schemas/sitemap-video/1.1');

$writer->setIndent(2);
$writer->startDocument();
$writer->startElement('urlset');
$writer->writeAttribute('xmlns:video', 'http://www.google.com/schemas/sitemap-video/1.1');

foreach ($videos as $video) {
  $writer->startElement('url');
  $writer->writeElement('loc', $video['url']);
  $writer->startElement('video:video');
  $writer->writeElement('video:title', $video['title']);
  $writer->endElement();
  $writer->endElement();
}
$writer->endElement();
$writer->endDocument();