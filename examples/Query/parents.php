<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
require __DIR__.'/../../vendor/autoload.php';

header('Content-type: text/plain');

$html = <<<HTML
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
HTML;

$fd = FluentDOM($html);
$parents = implode(
  ', ',
  $fd
    ->find('//b')
    ->parents()
    ->map(
        create_function('$node', 'return $node->tagName;')
      )
);
echo $fd
  ->find('//b')
  ->append('<strong>'.htmlspecialchars($parents).'</strong>');
