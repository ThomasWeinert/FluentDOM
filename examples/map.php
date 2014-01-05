<?php
/**
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
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
XML;

require_once('../src/FluentDOM.php');
$dom = FluentDOM::Query($xml);
echo $dom
  ->find('//p')
  ->append(
    implode(
      ', ',
      $dom
        ->find('//input')
        ->map(
          function($node, $index) {
            return FluentDOM::Query($node)->attr('value');
          }
        )
    )
  );
