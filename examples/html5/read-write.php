<?php
require_once(__DIR__.'/../../vendor/autoload.php');

$html = <<< 'HERE'
  <html>
  <head>
    <title>TEST</title>
  </head>
  <body id='foo'>
    <h1>Hello World</h1>
    <p>This is a test of the HTML5 parser.</p>
  </body>
  </html>
HERE;

/*
 * content type html5 or text/html5 trigger the HTML5 loader
 */
if ($fd = FluentDOM::load($html, 'html5')) {

  /*
   * the prefix html is registered for the namespace "http://www.w3.org/1999/xhtml"
   */
  echo $fd('string(//*[@id="foo"]//html:h1)');
  echo "\n\n";

  echo $fd->saveXml();

  echo "\n\n";
  echo new FluentDOM\Serializer\Html5($fd);
} else {
  echo 'Could not load HTML5 string. Is the "masterminds/html5" package installed?';
}


