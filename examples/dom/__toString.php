<?php
require_once(__DIR__.'/../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->loadXml('<xml>Hello World!</xml>');
echo $dom->documentElement->firstChild;

$dom = new FluentDOM\Document();
$dom->loadXml('<xml><![CDATA[Hello World!]]></xml>');
echo $dom->documentElement->firstChild;

$dom = new FluentDOM\Document();
$dom->loadXml('<xml attr="Hello World!"/>');
echo $dom->documentElement->getAttributeNode('attr');

$dom = new FluentDOM\Document();
$dom->loadXml('<xml><!--Hello World!--></xml>');
echo $dom->documentElement->firstChild;