$Id$

--------------------------------------------------------------------------------
 FluentDOM
 Version 2.0 
--------------------------------------------------------------------------------

FluentDOM provides an easy to use fluent interface for DOMDocument.

The idea was born in a workshop of Tobias Schlitt (schlitt.info) about the PHP
XML extensions at the IPC Spring in Berlin. He used this idea to show XPath
samples in the session. Since then he contributed several ideas and hints.

--------------------------------------------------------------------------------

Table Of Contents:
* URLs
* Requirements
* Usage
* Similarities With jQuery
* Differences To jQuery
* Classes
* Syntax Sugar
* Loaders
* Feedback
* Changelog
* Plans/ToDo

--------------------------------------------------------------------------------
 URLs
--------------------------------------------------------------------------------

Website: http://fluentdom.org

--------------------------------------------------------------------------------
 Requirements
--------------------------------------------------------------------------------

1) PHP >= 5.2 

FluentDOM needs at least PHP 5.2 but PHP 5.3 is suggested. Closures are
supported.

--------------------------------------------------------------------------------
 Usage
--------------------------------------------------------------------------------

echo FluentDOM('sample.xml')
  ->find('//h1[@id = "title"]')
  ->text('Hello World!');
  
The sample create a new FluentDOM object, loads the sample.xml file, looks for
an tag <h1> with the attribute "id" that has the value "title", sets the
content of this tag to "Hello World" and outputs the manipulated document.

--------------------------------------------------------------------------------
 Similarities With jQuery
--------------------------------------------------------------------------------

FluentDOM was created after the jQuery API and concepts. You will notice that
the most method names and parameters are the same.

Many thanks to the jQuery (jquery.com) people for their work, who did an
exceptional job describing their interfaces and providing examples. This saved
us a lot of work. We implemented most of the jQuery methods into FluentDOM

To be able to write PHPUnit Tests and develop FluentDOM a lot of examples where
written. Most of them are copied and adapted from or are deeply inspired by the
jQuery documentation. They are located in the 'examples' folder. 
Once again many thanks to the jQuery team.

--------------------------------------------------------------------------------
 Differences To jQuery
--------------------------------------------------------------------------------

1) XPath selectors

Every method that supports a selector uses XPath not CSS selectors. Since XPath
is supported by the ext/xml extension, no extra parsing need to be
done. This should be faster processing the selectors and btw it was easier for
us to implement. And as a nice topping it supports namespaces, too.

2) Text nodes

With a few exceptions FluentDOM handles text nodes just like element nodes.
You can select, traverse and manipulate them.

3) FluentDOM(), FluentDOMStyle()

Functions that create a object of the same class. You can provide $source and
$contentType paramters to initialize the content. Makes the chaining nicer. 

$fd = FluentDOM($source)->find($selector);

4) FluentDOM::xml()

A method to read an write the inner XML of the selected nodes.

5) FluentDOM::node()

This method provides functionality like jQuery's magic "$()" function to
import nodes to the current document. Because here is already a document object,
the method has only one parameter.

--------------------------------------------------------------------------------
 Classes
--------------------------------------------------------------------------------

1) FluentDOM

The main class. Supports most of jQuerys selection, traversing and manipulation
methods.

2) FluentDOMStyle

Extends FluentDOM with support wor manipulation of the style attribute.

3) FluentDOMIterator

Iterator for FluentDOM objects. Used for syntax sugar.

4) FluentDOMLoader

Interface for loader classes. They are used to import document content from
all kinds of sources.

--------------------------------------------------------------------------------
 Syntax Sugar
--------------------------------------------------------------------------------

FluentDOM supports a lot of PHP's syntax sugar implementing interfaces.
You can traverse selected elements using "foreach($fd as $node)", access them
with "$fd[$i]" syntax or use "count($fd)".

We support the string conversion using the magic __toString() method. It will
output the xml (or html) of the associated DOMDocument.

--------------------------------------------------------------------------------
 Loaders
--------------------------------------------------------------------------------

FluentDOM uses loader classes to import document content. If you do not set
an own loader object it initializes a default list of loader objects. They
provide support for the usual stuff like XML and HTML files and strings.
 
--------------------------------------------------------------------------------
 Changelog
--------------------------------------------------------------------------------

Version 2.0
- added: FluentDOMIterator
- added: FluentDOM now implements IteratorAggregate
- removed: FluentDOM does not implement RecursiveIterator any more
- removed: FluentDOM does not implement SeekableIterator any more
- removed: suffix "Siblings" from FluentDOM::next()
- removed: suffix "Siblings" from FluentDOM::nextAll()
- removed: suffix "Siblings" from FluentDOM::prev()
- removed: suffix "Siblings" from FluentDOM::prevAll()
- changed: FluentDOM::__construct() has no parameters any more
- changed: FluentDOM::append() now works on an empty document and returns new
           elements
- added: FluentDOM::load() to load document content
- added: FluentDOM::setLoaders() to set own loaders
- added: FluentDOMLoader interface
- added: FluentDOMLoaderXMLFile - load xml files
- added: FluentDOMLoaderXMLString - load xml strings
- added: FluentDOMLoaderHTMLFile - load html files
- added: FluentDOMLoaderHTMLString - load html strings
- added: FluentDOMLoaderDOMDocument - attach dom document
- added: FluentDOMLoaderDOMNode - attach owner document and select node
- added: FluentDOMLoaderSimpleXMLElement - import SimpleXML element
- added: Example for custom loader (example/iniloader)
- added: Example attributes with namespace
- added: FluentDOM::contentType - content type property for input and output
- fixed: attribute name check allowed invalid attribute names

Version 1.0
- initial release

--------------------------------------------------------------------------------
 Plans/ToDo
--------------------------------------------------------------------------------

1) CSS Selectors

We whould like to add a CSS to XPath translator.
