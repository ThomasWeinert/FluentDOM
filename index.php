<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(dirname(__FILE__).'/FluentDOM.php');
$doc = new DOMDocument();
$doc->load('test.xml');

$query = new FluentDOM($doc);
var_dump($query);

$query->find('//foo')
      ->find('../.')
      ->andSelf()
      ->end()
      ->attr(array('x' => 1, 'y' => 2))
      ->removeAttr('y')
      ->end()
      ->addClass('world earth')
      ->toggleClass('world continent');
             
echo htmlspecialchars($doc->saveXML()), '<br>';

$query->find('/test/*')
      ->xml('<bla>blub</bla>')
      ->find('bla')
      ->addClass('blob')
      ->prepend('HUHU');

echo htmlspecialchars($doc->saveXML()), '<br>';

$query->find('//foo')->appendTo('//bar//*');

echo htmlspecialchars($doc->saveXML()), '<br>';
?>