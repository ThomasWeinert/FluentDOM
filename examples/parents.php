<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
  <head>
    <title>Examples: FluentDOM\Query::parents()</title>
  </head>
  <body>
    <div>
      <p>
        <span>
          <b>My parents are: </b>
        </span>
      </p>
    </div>
  </body>
</html>
XML;

require_once('../src/FluentDOM.php');
$dom = FluentDOM::Query($xml);
$parents = implode(
  ', ',
  $dom
    ->find('//b')
    ->parents()
    ->map(
        create_function('$node', 'return $node->tagName;')
      )
);
echo $dom
  ->find('//b')
  ->append('<strong>'.htmlspecialchars($parents).'</strong>');
