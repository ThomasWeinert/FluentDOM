# FluentDOM

[![CI](https://github.com/ThomasWeinert/FluentDOM/actions/workflows/ci.yml/badge.svg)](https://github.com/ThomasWeinert/FluentDOM/actions/workflows/ci.yml)

[comment]: <> ([![Code Coverage]&#40;https://img.shields.io/scrutinizer/coverage/g/fluentdom/fluentdom.svg&#41;]&#40;https://scrutinizer-ci.com/g/FluentDOM/FluentDOM/?branch=master&#41;)
[comment]: <> ([![Scrutinizer Code Quality]&#40;https://img.shields.io/scrutinizer/g/fluentdom/fluentdom.svg&#41;]&#40;https://scrutinizer-ci.com/g/FluentDOM/FluentDOM/?branch=master&#41;)

[![License](https://img.shields.io/packagist/l/fluentdom/fluentdom.svg)](http://opensource.org/licenses/mit-license.php)
[![Total Downloads](https://img.shields.io/packagist/dt/fluentdom/fluentdom.svg)](https://packagist.org/packages/fluentdom/fluentdom)
[![Latest Stable Version](https://img.shields.io/packagist/v/fluentdom/fluentdom.svg)](https://packagist.org/packages/fluentdom/fluentdom)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/fluentdom/fluentdom.svg)](https://packagist.org/packages/fluentdom/fluentdom)

Copyright: 2009-2018 [FluentDOM Contributors](https://github.com/ThomasWeinert/FluentDOM/graphs/contributors)<br />
License: [The MIT License](http://www.opensource.org/licenses/mit-license.php) <br />

FluentDOM provides extended XML handling classes for PHPs DOM, XMLReader and XMLWriter.
Additionally, it contains a easy to use jQuery like, fluent interface for DOM.

Here are loaders and serializers for different formats like JSON, YAML, JsonML and others.
More (like HTML5) can be installed as additional packages.

FluentDOM is a test driven project. We write tests before and during the
development. You will find the PHPUnit tests in the `tests/` subdirectory.

## Table Of Contents
* Examples
* Support
* Requirements
* Packagist
* Usage
* Backwards Compatibility Breaks

## Examples

Many examples can be found in the `examples/` subdirectory. Here are
some for an initial impression:

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

### Read Large XML Files (FluentDOM 6.2)

```php
$reader = new FluentDOM\XMLReader();
$reader->open('sitemap.xml');
$reader->registerNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');

foreach (new FluentDOM\XMLReader\SiblingIterator($reader, 's:url') as $url) {
  /** @var FluentDOM\DOM\Element $url */
  var_dump(
    [
      'location' => $url('string(s:loc)'),
      'updated' => $url('string(s:lastmod)')
    ]
  );
}
```

## Support

The [wiki](https://github.com/ThomasWeinert/FluentDOM/wiki) provides information and usage examples.

If you find a bug or have a feature request please report it in the [issue tracker](https://github.com/ThomasWeinert/FluentDOM/issues).

You can check out the [![Gitter chat](https://img.shields.io/badge/gitter-join--chat-blue.svg)](https://gitter.im/ThomasWeinert/FluentDOM), too.

Be ware that the release packages (downloads) do not include the examples or tests. They are not needed
to use the library. If you clone the repository, they will be included.


### Security Issues

If you find a bug that has security implications, you can send an email directly to `thomas@weinert.info`.

## Requirements

### PHP

 * PHP >= 7.0
 * ext/dom
 * Optional
    * ext/xmlreader
    * ext/xmlwriter

FluentDOM needs at least PHP 7.0 and the DOM extension. For some features
additional extensions might be needed, like ext/json to load JSON strings.

To use the extended XMLReader/XMLWriter you will need the respective PHP extensions,
of course.

### HHVM

FluentDOM 5.2 and later requires HHVM 3.5.

FluentDOM 4.0 to 5.1 work with HHVM 3.3 but it was limited. If you like to use
HHVM it is strongly suggest to use newer releases.

FluentDOM 7.0 and later has not support for HHVM any more.

## Packagist

FluentDOM is available on [Packagist.org](https://packagist.org/packages/fluentdom/fluentdom), 
just add the dependency to your composer.json.

```javascript
{
  "require": {
    "fluentdom/fluentdom": "^7.0"
  }
}
```

## Usage

The following examples load the sample.xml file,
look for tags &lt;h1> with the attribute "id" that has the value "title",
set the content of these tags to "Hello World" and output the manipulated
document.

### Extended DOM (FluentDOM >= 5.2)

Using the `FluentDOM\Document` class:

```php
$fd = FluentDOM::load('sample.xml');
foreach ($fd('//h1[@id = "title"]') as $node) {
  $node->nodeValue = 'Hello World!';
}

echo $fd->saveXml();
```

### jQuery Style API

Using the `FluentDOM\Query` class:

```php
echo FluentDOM('sample.xml')
  ->find('//h1[@id = "title"]')
  ->text('Hello World!');
```

#### CSS Selectors

If you install a CSS selector to Xpath translation library into a project,
you can use the `FluentDOM::QueryCss()` function. It returns a `FluentDOM\Query` instance
supporting CSS 3 selectors.

```php
$fd = FluentDOM::QueryCss('sample.xml')
  ->find('h1#title')
  ->text('Hello World!');
```

Read more about it in the [Wiki](https://github.com/ThomasWeinert/FluentDOM/wiki/CSS-Selectors)

### Creating XML

New features in FluentDOM make it easy to create XML, even XML with namespaces. Basically
you can register XML namespaces on the document and methods without direct namespace support 
(like `createElement()`) will resolve the namespace and call the namespace aware variant 
(like `createElementNS()`).

Check the Wiki for an [example](https://github.com/ThomasWeinert/FluentDOM/wiki/Creating-XML-with-Namespaces-%28Atom%29).

## Backwards Compatibility Breaks

### From 6.2 to 7.0

The minimum required PHP version now is 7.0. HHVM is not supported any more.
Scalar type hints and return types were added.

Moved the extended DOM classes into the `FluentDOM\DOM` namespace. 
(`FluentDOM\Document` -> `FluentDOM\DOM\Document`). `FluentDOM\Nodes\Creator` was moved
to `FluentDOM\Creator`. Several internal classes were moved into a `FluentDOM\Utiltity`
namespace.

`FluentDOM\Query::get()` now return a `DOMNode`is the position was provided, not an array
any more.

`FluentDOM\DOM\Element::find()` was removed, use `FluentDOM($element)->find()`.

[Previous BC breaks](https://github.com/ThomasWeinert/FluentDOM/wiki/Backwards-Compatibility) are documented in the Wiki.

