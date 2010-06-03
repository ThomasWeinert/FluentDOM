<?php
/**
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <div>
    <p>Hello</p>
    <p>cruel</p>
    <p>World!</p>
  </div>
</body>
</html>
XML;

require_once('../src/FluentDOM.php');

/*
 * replace text content of 2nd paragraph
 */
echo FluentDOM($xml)
  ->find('//p[position() = 2]')
  ->text('nice');

echo "\n\n";

/*
 * replace text content of every paragraph
 */
echo FluentDOM($xml)
  ->find('//p')
  ->text('nice');

echo "\n\n";

/*
 * This explict example is to verify a PHP 'malloc' error thrown on MacOsX 10.5.7
 * when running test with PHPUnit3.3 - unfortunately it won't appear here
 *
 * replace text content of 2nd paragraph
 */
echo FluentDOM($xml)
  ->find('//p[position() = 1]')
  ->next()
  ->text('nice');
?>