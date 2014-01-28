--------------------------------------------------------------------------------
 FluentDOM

 Version: 5 dev
 Copyright: 2009-2014 Bastian Feder, Thomas Weinert
 Licence: The MIT License
          http://www.opensource.org/licenses/mit-license.php
--------------------------------------------------------------------------------

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
XML namespace support.

--------------------------------------------------------------------------------

Table Of Contents:
* URLs
* Requirements
* Usage
* Similarities With jQuery
* Differences To jQuery
* Backwards Compatibility Breaks

--------------------------------------------------------------------------------
 URLs
--------------------------------------------------------------------------------

Website: http://fluentdom.org

--------------------------------------------------------------------------------
 Requirements
--------------------------------------------------------------------------------

1) PHP >= 5.4

FluentDOM needs at least PHP 5.4.

--------------------------------------------------------------------------------
 Usage
--------------------------------------------------------------------------------

echo FluentDOM::Query('sample.xml')
  ->find('//h1[@id = "title"]')
  ->text('Hello World!');

The sample creates a new FluentDOM query object, loads the sample.xml file,
looks for a tag <h1> with the attribute "id" that has the value "title",
sets the content of this tag to "Hello World" and outputs the manipulated
document.

--------------------------------------------------------------------------------
 Similarities With jQuery
--------------------------------------------------------------------------------

FluentDOM was created after the jQuery API and concepts. You will notice that
the most method names and parameters are the same.

Many thanks to the jQuery (jquery.com) people for their work, who did an
exceptional job describing their interfaces and providing examples. This saved
us a lot of work. We implemented most of the jQuery methods into FluentDOM

To be able to write PHPUnit Tests and develop FluentDOM a lot of examples were
written. Most of them are copied and adapted from or are deeply inspired by the
jQuery documentation. They are located in the 'examples' folder.
Once again many thanks to the jQuery team.

--------------------------------------------------------------------------------
 Major Differences To jQuery
--------------------------------------------------------------------------------

1) XPath selectors

Every method that supports a selector uses XPath not CSS selectors. Since XPath
is supported by the ext/xml extension, no extra parsing need to be
done. This should be faster processing the selectors and btw it was easier for
us to implement. And as a nice topping it supports namespaces, too.

2) Text nodes

With a few exceptions FluentDOM handles text nodes just like element nodes.
You can select, traverse and manipulate them.

--------------------------------------------------------------------------------
 Backwards Compatibility Breaks To FluentDOM 4.x
--------------------------------------------------------------------------------

Version 5 is a major rewrite. It now uses php namespaces. The original FluentDOM
classes (FluentDOM, FluentDOMCore and FluentDOMStyle) are merged into the new
FluentDOM\Query class. Here is a FluentDOM class in the global namespace with
factory functions. FluentDOM::query() creates a new Fluent\Query instance.

New classes extend the existing DOM classes of PHP to provide convenience and
work around bugs.

The old Loaders are gone and replaced with the new FluentDOM\Loadable interface.

The registerNamespaces() method was replaced with a registerNamespace() method,
having the same arguments like DOMXpath::registerNamespace().
