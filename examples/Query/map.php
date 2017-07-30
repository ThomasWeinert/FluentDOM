<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2017 FluentDOM Contributors
*/
header('Content-type: text/plain');
require_once '../../vendor/autoload.php';

$html = <<<HTML
<html>
  <head>
    <title>Examples: FluentDOM\Query::map()</title>
  </head>
  <body>
    <p><b>Values: </b></p>
    <form>
      <input type="text" name="name" value="John"/>
      <input type="text" name="password" value="password"/>
      <input type="text" name="url" value="http://ejohn.org/"/>
    </form>
  </body>
</html>
HTML;

$fd = FluentDOM($html);
echo $fd
  ->find('//p')
  ->append(
    implode(
      ', ',
      $fd
        ->find('//input')
        ->map(
          function($node, $index) {
            return FluentDOM($node)->attr('value');
          }
        )
    )
  );
