# FluentDOM

[![Build Status](https://travis-ci.org/FluentDOM/FluentDOM.svg?branch=master)](https://travis-ci.org/FluentDOM/FluentDOM)
[![HHVM Status](http://hhvm.h4cc.de/badge/fluentdom/fluentdom.png)](http://hhvm.h4cc.de/package/fluentdom/fluentdom)
[![License](https://poser.pugx.org/fluentdom/fluentdom/license.svg)](https://packagist.org/packages/fluentdom/fluentdom)
[![Total Downloads](https://poser.pugx.org/fluentdom/fluentdom/downloads.svg)](https://packagist.org/packages/fluentdom/fluentdom)
[![Latest Stable Version](https://poser.pugx.org/fluentdom/fluentdom/v/stable.svg)](https://packagist.org/packages/fluentdom/fluentdom)
[![Latest Unstable Version](https://poser.pugx.org/fluentdom/fluentdom/v/unstable.svg)](https://packagist.org/packages/fluentdom/fluentdom)

  Version: 5<br />
  Copyright: 2009-2014 Bastian Feder, Thomas Weinert <br />
  Licence: [The MIT License](http://www.opensource.org/licenses/mit-license.php) <br />

FluentDOM provides an easy to use fluent interface for DOMDocument. We tried to
keep the jQuery API but adapted it to PHP and the server environment.

The idea was born in a workshop of Tobias Schlitt (http://schlitt.info) about
the PHP XML extensions at the IPC Spring in Berlin. He used this idea to show
XPath samples in the session. Since then he contributed several ideas and hints.
The loader concept was his idea, too.

FluentDOM is a test driven project. We write tests before and during the
development. You will find the PHPUnit test in the "tests" subdirectory.

Version 5 is a complete rewrite. It is updated to the new PHP 5.4 syntax. It
now provides classes that extend PHPs DOMDocument. Another focus was
XML namespace support for document creation.

## Table Of Contents:
* Requirements
* Usage
* Packagist
* Similarities With jQuery
* Differences To jQuery
* Extensions to PHPs DOM classes
* Backwards Compatibility Breaks
* CSS 3 Selectors

## Requirements

### PHP

 * PHP >= 5.4
 * ext/dom

FluentDOM needs at least PHP 5.4 and the DOM extension. For some features
additional extensions might be needed, like ext/json to load JSON strings.

### HHVM

 * HHVM >= 3.0

FluentDOM works with HHVM 3.0. Here is an [issue](https://github.com/facebook/hhvm/issues/1848)
with DOMDocument::registerNodeClass() and DOMDocument::createElement() at the moment,
but FluentDOM\Document includes a workaround.

The automatic namespace registration can not be disabled at the moment, HHVM does not
support the 3rd argument for DOMXPath::evaluate(). FluentDOM\Xpath will
ignore it.

## Usage

```php
<?php
echo FluentDOM('sample.xml')
  ->find('//h1[@id = "title"]')
  ->text('Hello World!');
?>
```

The sample creates a new FluentDOM query object, loads the sample.xml file,
looks for a tag &lt;h1> with the attribute "id" that has the value "title",
sets the content of this tag to "Hello World" and outputs the manipulated
document.

## Packagist

FluentDOM is available on [Packagist.org](https://packagist.org/packages/fluentdom/fluentdom), just add the dependency to your composer.json.

```javascript
{
  "require" : {
    "fluentdom/fluentdom": "5.x"
  }
}
```

## Similarities With jQuery

FluentDOM was created after the jQuery API and concepts. You will notice that
the most method names and parameters are the same.

Many thanks to the jQuery (jquery.com) people for their work, who did an
exceptional job describing their interfaces and providing examples. This saved
us a lot of work. We implemented most of the jQuery methods into FluentDOM

To be able to write PHPUnit Tests and develop FluentDOM a lot of examples were
written. Most of them are copied and adapted from or are deeply inspired by the
jQuery documentation. They are located in the 'examples' folder.
Once again many thanks to the jQuery team.

## Major Differences To jQuery

### 1) XPath selectors

By default every method that supports a selector uses XPath not CSS selectors.
Since XPath is supported by the ext/dom extension, no extra parsing need to be
done. This should be faster processing the selectors and btw it was easier to implement.

### 2) Text nodes

With a few exceptions FluentDOM handles text nodes just like element nodes.
You can select, traverse and manipulate them.

## Extensions to PHPs DOM classes

FluentDOM 5 provides extended variants of some of the DOM classes. Most of
it is dedicated to improve namespace handling, some works around known problems
and some is just for comfort.

You can register namespaces on the document. They will be used if elements
or attributes are created/updated and no explicit namespace is provided. You can
even register a default namespace for elements.

### Creating XML

This creates the example feed from the [RFC4287](http://tools.ietf.org/html/rfc4287#section-1.1)

```php
<?php
$dom = new FluentDOM\Document();
$dom->registerNamespace('#default', 'http://www.w3.org/2005/Atom');

$feed = $dom->appendElement('feed');
$feed->appendElement('title', 'Example Feed');
$feed->appendElement('link', NULL, ['href' => 'http://example.org/']);
$feed->appendElement('updated', '2003-12-13T18:30:02Z');
$feed->appendElement('author')->appendElement('name', 'John Doe');
$feed->appendElement('id', 'urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');

$entry = $feed->appendElement('entry');
$entry->appendElement('title', 'Atom-Powered Robots Run Amok');
$entry->appendElement('link', NULL, ['href' => 'http://example.org/2003/12/13/atom03']);
$entry->appendElement('id', 'urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
$entry->appendElement('updated', '2003-12-13T18:30:02Z');
$entry->appendElement('summary', 'Some text.');

$dom->formatOutput = TRUE;
echo $dom->saveXml();
?>
```

## Backwards Compatibility Breaks To &lt;5

Version 5 is a major rewrite. It now uses php namespaces. The original FluentDOM
classes (FluentDOM, FluentDOMCore and FluentDOMStyle) are merged into the new
FluentDOM\Query class.

The old loaders are gone and replaced with the new FluentDOM\Loadable interface.

The registerNamespaces() method was replaced with a registerNamespace() method,
having the same arguments like DOMXpath::registerNamespace().

## CSS 3 Selectors

If you install [PhpCss](https://github.com/ThomasWeinert/PhpCss) into a project,
you can use the FluentDOM::QueryCss() function. It returns a FluentDOM instance
supporting CSS 3 selectors.

```php
<?php
$fd = FluentDOM::QueryCss('sample.xml')
  ->find('h1#title')
  ->text('Hello World!');
?>
```
