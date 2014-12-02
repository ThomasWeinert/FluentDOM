# FluentDOM

[![License](https://poser.pugx.org/fluentdom/fluentdom/license.svg)](http://www.opensource.org/licenses/mit-license.php)
[![Build Status](https://travis-ci.org/FluentDOM/FluentDOM.svg?branch=master)](https://travis-ci.org/FluentDOM/FluentDOM)
[![HHVM Status](http://hhvm.h4cc.de/badge/fluentdom/fluentdom.png)](http://hhvm.h4cc.de/package/fluentdom/fluentdom)
[![Code Coverage](https://scrutinizer-ci.com/g/FluentDOM/FluentDOM/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/FluentDOM/FluentDOM/?branch=master)
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

## Table Of Contents
* Requirements
* Usage
* Packagist
* Support
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

FluentDOM 5.2 (and the current development master) requires HHVM 3.5

FluentDOM 4.0 to 5.1 work with HHVM 3.3 but it is limited.

HHVM has some issues with the with DOMDocument::registerNodeClass() at the moment.
FluentDOM\Document includes a workaround, but here is no guarantee.

https://github.com/facebook/hhvm/issues/1848
https://github.com/facebook/hhvm/issues/2962

The automatic namespace registration can not be disabled at the moment, HHVM does not
support the 3rd argument for DOMXPath::evaluate(). FluentDOM\Xpath will
ignore it.

https://github.com/facebook/hhvm/issues/2810

## Usage

The first two samples create a new FluentDOM object, load the sample.xml file,
look for tags &lt;h1> with the attribute "id" that has the value "title",
set the content of these tags to "Hello World" and output the manipulated
document.

### jQuery Style API

```php
<?php
echo FluentDOM('sample.xml')
  ->find('//h1[@id = "title"]')
  ->text('Hello World!');
```

### Extended DOM (FluentDOM 5.2)

```php
<?php
$fd = FluentDOM::load('sample.xml');
foreach ($fd('//h1[@id = "title"]') as $node) {
  $node->nodeValue = 'Hello World!';
}

echo $fd->saveXml();
```

### Creating XML

New features in FluentDOM 5 make it easy to create XML, even XML with namespaces. Basically 
you can register XML namespaces on the document and methods without direct namespace support 
(like createElement()) will resolve the namespace and call the namespace aware variant 
(like createElementNS()).

Check the Wiki for an [example](https://github.com/FluentDOM/FluentDOM/wiki/Creating-XML-with-Namespaces-%28Atom%29).

## Packagist

FluentDOM is available on [Packagist.org](https://packagist.org/packages/fluentdom/fluentdom), 
just add the dependency to your composer.json.

```javascript
{
  "require" : {
    "fluentdom/fluentdom": "5.x"
  }
}
```

## Support

The [wiki](https://github.com/FluentDOM/FluentDOM/wiki) provides information and usage examples.

If you find a bug or have a feature request please report it in the [issue tracker](https://github.com/FluentDOM/FluentDOM/issues).

You can check out the [![Gitter chat](https://badges.gitter.im/FluentDOM/FluentDOM.png)](https://gitter.im/FluentDOM/FluentDOM), too.


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

But FluentDOM 5 can use CSS selectors with the help of a converter library.

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

## Backwards Compatibility Breaks
 
### From 4 To 5

Version 5 is a major rewrite. It now uses php namespaces. The original FluentDOM
classes (FluentDOM, FluentDOMCore and FluentDOMStyle) are merged into the new
FluentDOM\Query class.

The old loaders are gone and replaced with the new FluentDOM\Loadable interface.

The registerNamespaces() method was replaced with a registerNamespace() method,
having the same arguments like DOMXpath::registerNamespace().

### From 5.1 To 5.2

The FluentDOM\Loadable::load() method now has a third argument $options. The
FluentDOM\Nodes method and the FluentDOM function that load data sources got this
argument, too. It allows to specify additional, loader specific options. The
values are only used inside the loader. This change affects the implementation of
loaders, but not the use. 

## CSS 3 Selectors

If you install a CSS selector to Xpath translation library into a project,
you can use the FluentDOM::QueryCss() function. It returns a FluentDOM instance
supporting CSS 3 selectors.

```php
<?php
$fd = FluentDOM::QueryCss('sample.xml')
  ->find('h1#title')
  ->text('Hello World!');
```

Two libraries are supported:

  1. [Carica/PhpCss](https://github.com/ThomasWeinert/PhpCss)
  2. [Symfony/CssSelector](https://github.com/symfony/CssSelector)

