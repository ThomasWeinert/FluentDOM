<?php
require(__DIR__.'/../../vendor/autoload.php');

/*
 * SimpleXML has some limited functionality for this, basically you
 * can add child elements.
 */
$element = simplexml_load_string('<ul/>');
$li = $element->addChild('li', 'FluentDOM');
$li['href'] = 'http://fluentdom.org';
echo $element->saveXml();

/*
 * FluentDOM extends DOM for that, so you still can use all
 * the DOM methods to create, insert and replace nodes.
 */
$document = new FluentDOM\Document();
$ul = $document->appendChild($document->createElement('ul'));
$li = $ul->appendChild($document->createElement('li', 'FluentDOM', ['href' => 'http://fluentdom.org']));
echo $document->saveHtml();

/*
 * But here is a shortcut to add an element child.
 */
$document = new FluentDOM\Document();
$ul = $document->appendElement('ul');
$li = $ul->appendElement('li', 'FluentDOM', ['href' => 'http://fluentdom.org']);
echo $document->saveHtml();

/*
 * Additionally it has a dedicated API to create documents.
 * The Creator is a function object. The first argument is the element name, the
 * following arguments are attributes (array) or child nodes (scalars, nodes, ...).
 * The calls can be nested, resulting in a really compact syntax.
 *
 * Methods of the Creator allow to add special nodes like comments.
 */
$_ = FluentDOM::create();
$document = $_(
  'ul',
  $_('li', ['href' => 'http://fluentdom.org'], 'FluentDOM')
)->document;
echo $document->saveHtml();

