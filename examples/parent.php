<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::parent()</title>
  </head>
  <body>
    <div>div,
      <span>span, </span>
      <b>b </b>
    </div>
    <p>p,
      <span>span,
        <em>em </em>
      </span>
    </p>
    <div>div,
      <strong>strong,
        <span>span, </span>
        <em>em,
          <b>b, </b>
        </em>
      </strong>
      <b>b </b>
    </div>
  </body>
</html>
XML;

require_once('../vendor/autoload.php');

echo FluentDOM($xml)
  ->find('//body//*')
  ->each('callback');


function callback($node) {
  $fluentNode = FluentDOM($node);
  $fluentNode->prepend(
    $fluentNode->document->createTextNode(
      $fluentNode->parent()->item(0)->tagName.' > '
    )
  );
}
