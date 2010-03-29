<?php
// include FluentDOM
require('../../FluentDOM.php');
// define url
$url = 'http://www.papaya-cms.com/';
// load data from an url
$html = file_get_contents($url);

$fd = FluentDOM($html, 'html')
  // find links
  ->find('//a[@href]')
  ->each(
    function ($node) use ($url) {
      //convert node to FluentDOM
      $item = FluentDOM($node);
      // check for relative url
      if (!preg_match('(^[a-zA-Z]+://)', $item->attr('href'))) {
        // add base url
        $item->attr('href', $url.$item->attr('href'));
      }
    }
  );
  
// change content type  
$fd->contentType = 'xml';
// send content type header
header('Content-type: text/xml');
// output new document
echo $fd;
?>