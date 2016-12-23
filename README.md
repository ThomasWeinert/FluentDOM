# FluentDOM

[![Build Status](https://img.shields.io/travis/FluentDOM/FluentDOM.svg)](https://travis-ci.org/FluentDOM/FluentDOM)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/fluentdom/fluentdom.svg)](https://scrutinizer-ci.com/g/FluentDOM/FluentDOM/?branch=master)

[![License](https://img.shields.io/packagist/l/fluentdom/fluentdom.svg)](http://opensource.org/licenses/mit-license.php)
[![HHVM Status](https://img.shields.io/hhvm/fluentdom/fluentdom.svg)](http://hhvm.h4cc.de/package/fluentdom/fluentdom)
[![Total Downloads](https://img.shields.io/packagist/dt/fluentdom/fluentdom.svg)](https://packagist.org/packages/fluentdom/fluentdom)
[![Latest Stable Version](https://img.shields.io/packagist/v/fluentdom/fluentdom.svg)](https://packagist.org/packages/fluentdom/fluentdom)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/fluentdom/fluentdom.svg)](https://packagist.org/packages/fluentdom/fluentdom)

  Copyright: 2009-2016 Bastian Feder, Thomas Weinert <br />
  Licence: [The MIT License](http://www.opensource.org/licenses/mit-license.php) <br />

FluentDOM provides an easy to use fluent interface for DOMDocument. We tried to
keep the jQuery API but adapted it to PHP and the server environment.

The idea was born in a workshop of Tobias Schlitt (http://schlitt.info) about
the PHP XML extensions at the IPC Spring in Berlin. He used this idea to show
XPath samples in the session. Since then he contributed several ideas and hints.
The basic loader concept was his idea, too.

FluentDOM is a test driven project. We write tests before and during the
development. You will find the PHPUnit test in the "tests" subdirectory.

Version 5 was a complete rewrite. It is updated to the new PHP 5.4 syntax. It
now provides classes that extend PHPs DOMDocument. Another focus was
XML namespace support for document creation.

Version 6 bumps the minimum required PHP version to 5.6. Mostly to make use of the
variadics syntax.

## Table Of Contents
* Support
* Requirements
* Packagist
* Usage
* jQuery Similarities & Differences
* Backwards Compatibility Breaks

## Examples

### Read All Links in a HTML File

```php
$document = FluentDOM::load(
  $htmlFile,
  'text/html',
  [FluentDOM\Loader\Options::ALLOW_FILE => TRUE]
);
foreach ($document('//a[@href]') as $a) {
  $links[] = [
    'caption' => (string)$a,
    'href' => $a['href']
  ];
}
var_dump($links);
```

### Create a Select From an Array

```php
$_ = FluentDOM::create();
$_->formatOutput = TRUE;
echo $_(
  'select',
  ['name' => 'example'],
  $_->each(
    ['One', 'Two', 'Three'],
    function($text, $index) use ($_) {
      return $_('option', ['value' => $index], $text);
    }
  )
)->document->saveHTML();
```


## Support

The [wiki](https://github.com/FluentDOM/FluentDOM/wiki) provides information and usage examples.

If you find a bug or have a feature request please report it in the [issue tracker](https://github.com/FluentDOM/FluentDOM/issues).

You can check out the [![Gitter chat](https://img.shields.io/badge/gitter-join--chat-blue.svg)](https://gitter.im/FluentDOM/FluentDOM), too.

Be ware that the release packages (downloads) do not include the examples or tests. They are not needed
to use the library. If you clone the repository, they will be included.


### Security Issues

If you find a bug that has security implications, you can send an email directly to `thomas@weinert.info`.

## Requirements

### PHP

 * PHP >= 5.6
 * ext/dom

FluentDOM needs at least PHP 5.6 and the DOM extension. For some features
additional extensions might be needed, like ext/json to load JSON strings.

### HHVM

FluentDOM 5.2 and later requires HHVM 3.5.

FluentDOM 4.0 to 5.1 work with HHVM 3.3 but it was limited. If you like to use
HHVM it is strongly suggest to use newer releases.

## Packagist

FluentDOM is available on [Packagist.org](https://packagist.org/packages/fluentdom/fluentdom), 
just add the dependency to your composer.json.

```javascript
{
  "require" : {
    "fluentdom/fluentdom": "^6.0"
  }
}
```

### CSS Selectors

To use CSS selectors, you need a CSS to XPath library.

#### FluentDOM >= 5.3

Here is a new interface `FluentDOM\Xpath\Transformer` which is implemented in 
separate connector packages. Two are currently available.

  1. [FluentDOM/Selectors-PHPCss](https://github.com/FluentDOM/Selectors-PHPCss)
  2. [FluentDOM/Selectors-Symfony](https://github.com/FluentDOM/Selectors-Symfony)
  
The packages provide a `fluentdom/css-selector` meta package.

### FluentDOM <= 5.2

Had fixed support for two CSS to XPath libraries. If they are installed in the project
CSS selects are available.

  1. [Carica/PhpCss](https://github.com/ThomasWeinert/PhpCss)
  2. [Symfony/CssSelector](https://github.com/symfony/css-selector)

## Usage

The examples load the sample.xml file,
look for tags &lt;h1> with the attribute "id" that has the value "title",
set the content of these tags to "Hello World" and output the manipulated
document.

### Extended DOM (FluentDOM >= 5.2)

Using the `FluentDOM\Document` class:

```php
<?php
$fd = FluentDOM::load('sample.xml');
foreach ($fd('//h1[@id = "title"]') as $node) {
  $node->nodeValue = 'Hello World!';
}

echo $fd->saveXml();
```

### jQuery Style API

Using the `FluentDOM\Query` class:

```php
<?php
echo FluentDOM('sample.xml')
  ->find('//h1[@id = "title"]')
  ->text('Hello World!');
```

### CSS Selectors

If you install a CSS selector to Xpath translation library into a project,
you can use the `FluentDOM::QueryCss()` function. It returns a `FluentDOM\Query` instance
supporting CSS 3 selectors.

```php
<?php
$fd = FluentDOM::QueryCss('sample.xml')
  ->find('h1#title')
  ->text('Hello World!');
```

### Creating XML

New features in FluentDOM make it easy to create XML, even XML with namespaces. Basically
you can register XML namespaces on the document and methods without direct namespace support 
(like `createElement()`) will resolve the namespace and call the namespace aware variant 
(like `createElementNS()`).

Check the Wiki for an [example](https://github.com/FluentDOM/FluentDOM/wiki/Creating-XML-with-Namespaces-%28Atom%29).

## jQuery

### Similarities

FluentDOM was created after the jQuery API and concepts. You will notice that
the most method names and parameters are the same.

Many thanks to the jQuery (jquery.com) people for their work, who did an
exceptional job describing their interfaces and providing examples. This saved
us a lot of work. We implemented most of the jQuery methods into FluentDOM

To be able to write PHPUnit Tests and develop FluentDOM a lot of examples were
written. Most of them are copied and adapted from or are deeply inspired by the
jQuery documentation. They are located in the 'examples' folder.
Once again many thanks to the jQuery team.

### Differences

#### XPath selectors

By default every method that supports a selector uses XPath not CSS selectors.
Since XPath is supported by the ext/dom extension, no extra parsing need to be
done. This should be faster processing the selectors and was easier to implement.

But FluentDOM can use CSS selectors with the help of a converter library.

#### Text nodes

With a few exceptions FluentDOM handles text nodes just like element nodes.
You can select, traverse and manipulate them.

#### Extensions to PHPs DOM classes

FluentDOM provides extended variants of some of the DOM classes. Most of
it is dedicated to improve namespace handling, some works around known problems
and some is just for comfort.

You can register namespaces on the document. They will be used if elements
or attributes are created/updated and no explicit namespace is provided. You can
even register a default namespace for elements.

## Backwards Compatibility Breaks

### From 5.3 to 6.0

The minimum required PHP version now is 5.6.

`FluentDOM\Query` now parses fragment arguments depending on the
content type. It uses the loaders to parse the fragments for methods like
`FluentDOM\Query::append()`. To parse the fragments as XML change the content type 
after loading.

```php
$fd = FluentDOM($content, 'type/some-type');
$fd->contentType = 'text/xml';
```

`FluentDOM\Query::attr()`, `FluentDOM\Query::css()` and `FluentDOM\Query::data()`
now recognize that the second argument is provided, even if it is NULL.

Serializer factories can now be registered on the FluentDOM class. Loaders implement
an additional method to parse a fragment. This allows the FluentDOM\Nodes() class
to keep the content type used to load a source. Methods like `append()` now parse
a string as a fragment of the current content type. Casting the FluentDOM\Nodes()
instance to a string serializes it to the current content type.

```php
$fd = FluentDOM('{"firstName": "John"}', 'text/json');
echo $fd->find('/*')->append('{"lastName": "Smith"}');
/*
 {"firstName":"John","lastName":"Smith"}
 */
```

To get the previous behaviour you will have to change the content type to 'text/xml' after
loading a source.

```php
$fd = FluentDOM('{"firstName": "John"}', 'text/json');
$fd->contentType = 'text/xml';
echo $fd->find('/*')->append('<lastName>Smith</lastName>');

/* 
  <json:json xmlns:json="urn:carica-json-dom.2013">
    <firstName>John</firstName>
    <lastName>Smith</lastName>
  </json:json>
 */
```

Loaders have an additional method loadFragment(). Serializers are now expected to be able to 
serialize a node (not only a document).

You will now have to explicitly allow loaders to load a file. 

```php
$fd = FluentDOM('...', '...', [FluentDOM\Loader\Options::ALLOW_FILE => TRUE]);
$fd = FluentDOM('...', '...', [FluentDOM\Loader\Options::IS_FILE => TRUE]);
```

### From 5.2 To 5.3

CSS Selectors are now provided by separate packages. If you like to use them
you will need to require the connector package now.

### From 5.1 To 5.2

The `FluentDOM\Loadable::load()` method now has a third argument $options. The
FluentDOM\Nodes method and the FluentDOM function that load data sources got this
argument, too. It allows to specify additional, loader specific options. The
values are only used inside the loader. This change affects the implementation of
loaders, but not the use. 
 
### From 4 To 5

Version 5 is a major rewrite. It now uses php namespaces. The original FluentDOM
classes (`FluentDOM`, `FluentDOMCore` and `FluentDOMStyle`) are merged into the new
`FluentDOM\Query` class.

The old loaders are gone and replaced with the new FluentDOM\Loadable interface.

The registerNamespaces() method was replaced with a registerNamespace() method,
having the same arguments like DOMXpath::registerNamespace().

