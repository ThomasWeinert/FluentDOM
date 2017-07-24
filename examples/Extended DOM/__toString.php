<?php
require_once(__DIR__.'/../../vendor/autoload.php');

/*
 * FluentDOM implements the __toString() method for several
 * of the node classes, allowing for implicit and explict string casts.
 */

$document = new FluentDOM\DOM\Document();
$document->loadXml('<xml>Hello World!</xml>');
echo $document->documentElement->firstChild;

$document = new FluentDOM\DOM\Document();
$document->loadXml('<xml><![CDATA[Hello World!]]></xml>');
echo $document->documentElement->firstChild;

$document = new FluentDOM\DOM\Document();
$document->loadXml('<xml attr="Hello World!"/>');
echo $document->documentElement->getAttributeNode('attr');

$document = new FluentDOM\DOM\Document();
$document->loadXml('<xml><!--Hello World!--></xml>');
echo $document->documentElement->firstChild;