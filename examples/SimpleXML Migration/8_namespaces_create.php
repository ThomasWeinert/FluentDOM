<?php
require(__DIR__.'/../../vendor/autoload.php');

/*
 * The third argument of SimpleXMLElement::addChild() is the namespace URI.
 * To create an element with a namespace provide it and optionally use a
 * prefix in the node name argument.
 *
 * However you need to start with by loading an valid document into SimpleXML.
 */
$element = simplexml_load_string('<atom:feed xmlns:atom="http://www.w3.org/2005/Atom"/>');
$title = $element->addChild('atom:title', 'FluentDOM Feed', 'http://www.w3.org/2005/Atom');
echo $element->saveXml();

/*
 * FluentDOM allows you register namespaces on the DOM document object.
 * All the the DOM methods will use it to resolve prefixes. It basically makes all
 * DOM methods namespace aware.
 */
$document = new FluentDOM\DOM\Document();
$document->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
$document
  ->appendElement('atom:feed')
  ->appendElement('atom:title', 'FluentDOM Feed');
echo $document->saveXml();

/*
 * That works with the Creator function object, too.
 */
$_ = FluentDOM::create();
$_->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
echo $_(
  'atom:feed',
  $_('atom:title', 'FluentDOM Feed')
);
