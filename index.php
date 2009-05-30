<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-type: text/plain');

require_once(dirname(__FILE__).'/FluentDOM.php');
$doc = new DOMDocument();
$doc->preserveWhiteSpace = FALSE;
$doc->formatOutput = TRUE;
$doc->load('test.xml');

$query = new FluentDOM($doc);

$query->find('//foo')
      ->find('../.')
      ->andSelf()
      ->end()
      ->attr(array('x' => 1, 'y' => 2))
      ->removeAttr('y')
      ->end()
      ->addClass('world earth')
      ->toggleClass('world continent');
             
echo $doc->saveXML(), "\n";

$query->find('/test/*')
      ->xml('<bla>blub</bla>')
      ->find('bla')
      ->addClass('blob');

echo $doc->saveXML(), "\n";

$query->find('//foo')->appendTo('//bar');

echo $doc->saveXML(), "\n";
?>