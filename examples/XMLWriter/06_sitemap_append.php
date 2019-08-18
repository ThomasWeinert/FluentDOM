<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

require __DIR__.'/../../vendor/autoload.php';

$xml = <<<'XML'
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
     <loc>http://www.example.com/some_page.html</loc>
     <lastmod>2019-07-01T21:33:53-07:00</lastmod>
     <priority>0.80</priority>
   </url>
</urlset>
XML;

$reader = new \FluentDOM\XMLReader();
$reader->open('data://text/plain;base64,' . base64_encode($xml));
$reader->registerNamespace('', 'http://www.sitemaps.org/schemas/sitemap/0.9');

$writer = new FluentDOM\XMLWriter();
$writer->openURI('php://stdout');
$writer->registerNamespace('', 'http://www.sitemaps.org/schemas/sitemap/0.9');

$writer->setIndent(2);
$writer->startDocument();
$writer->startElement('urlset');

// copy existing
foreach (new \FluentDOM\XMLReader\SiblingIterator($reader, 'url') as $url) {
  $writer->collapse($url);
}

// add new
$writer->startElement('url');
$writer->writeElement('loc',  'http://www.example.com/another_page.htm');
$writer->writeElement('lastmod',  date(DATE_ATOM));
$writer->writeElement('priority',  '0.5');
$writer->endElement();

$writer->endElement();
$writer->endDocument();
