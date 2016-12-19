<?php
// include FluentDOM
require('../../vendor/autoload.php');
// define url
$url = 'http://www.heise.de/';
// load data from an url
$html = file_get_contents($url);

$document = FluentDOM::load($html, 'html', [FluentDOM\Loader\Options::ALLOW_FILE => TRUE]);
foreach ($document('//a[@href]') as $a) {
  // check for relative url
  if (!preg_match('(^[a-zA-Z]+://)', $a['href'])) {
    // add base url
    $a['href'] = $url.$a['href'];
  }
}

// send content type header
header('Content-type: text/xml');
// output new document
echo $document->saveXML();
