<?php
require_once(dirname(__FILE__).'/FluentDOMDocument.php');
$doc = new FluentDOMDocument();
$doc->load('test.xml');

$doc->find('//foo')
    ->find('../.')
    ->andSelf()
    ->end()
    ->attr(array('x' => 1, 'y' => 2))
    ->removeAttr('y')
    ->end()
    ->addClass('world earth')
    ->toggleClass('world continent');
             
echo htmlspecialchars($doc->saveXML()), '<br>';

$doc->find('/test/*')
    ->xml('<bla>blub</bla>')
    ->find('bla')
    ->addClass('blob')
    ->prepend('HUHU');

echo htmlspecialchars($doc->saveXML()), '<br>';

$doc->find('//foo')->appendTo('//bar//*');

echo htmlspecialchars($doc->saveXML()), '<br>';
?>