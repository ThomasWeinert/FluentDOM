8.0.0
-----

- [!BC] Minimum PHP Version 7.2.0
- [!BC] \FluentDOM\Loadable::load() now always returns 
  \FluentDOM\Loader\Result instance (or NULL)
- [!BC] Define Return Type For \FluentDOM\Loadable::load()
- [!BC] Define Return Type For \FluentDOM\Loadable::loadFragment()
- [!BC] Define Appendable::appendTo() Return Value 
  as "void"
- [!BC] Change Method Signatures Of DOM L3 Methods To Match PHP
- [!BC] Declare FluentDOM::setLoader() Argument As A Nullable 
  Of Loadable
- [FEATURE] #90 Throw Exception If File Can Not Be Loaded
- [REFACTOR] Define Argument And Return Types
- [REFACTOR] Reference Loaders By Class Constants
- [REFACTOR] Cleanup Unit Tests

7.4.0
-----

- [DOC] Add XMLWriter Append Example
- [FEATURE] #87 Add FluentDOM\Query::unwrap()

7.3.0
-----

- [FEATURE] Add PRESERVE_WHITESPACE option to XML loaders

7.2.0
-----

- Set class files to strict types
- Implement #78: ParentNode::$childElementCount
- Implement #80: allow to remove namespace attributes
- Add inheritance to Node interfaces
- DocumentFragment implements the ParentNode interface
- DOM Element::setAttribute() has not return value (in W3C standard)
- Add Clark Notation to elements: FluentDOM\Element::clarkNotation
- Add Clark Notation to attributes: FluentDOM\DOM\Attribute::clarkNotation()
- Fix cdata section support for BadgerFish serializer
- Let \FluentDOM\Exception interface extend \Throwable
- Rename DOM\ChildNode::replace() to replaceWith() (DOM LS standard), keep replace for BC but mark as deprecated
- DOMCDATASection implements DOMText change condition order to fix handling in XMLWriter::collapse().
- Add constraint for node classes, FluentDOM\Utility\Constraints::assertNodeClass()
- Add: JsonDOM example with mapping array node name
- Fix phpdoc annotations
- Cleanup code and fix inspections
- Add missing unit tests

7.1.0
-----

- Add: FluentDOM\DOM\Implementation extended DOMImplementation
- Add: FluentDOM\DOM\Document::createDocumentType()
- Add: XMLWriter filter example
- Update: PHP 7.3 added to CI
- Test: loading html fragment using option Loader\Html::IS_FRAGMENT
- Fix inspection messages, function call optimization and return types
- Refactor: array_merge() to array_push()
- Fix: recursion handling in FluentDOM\Loader\Json\JsonDOM and FluentDOM\Loader\PHP\PDO

7.0.0
-----

- Changed: minimum PHP version is now 7.0
- Changed: Added type hints and return types
- Changed: Moved extended DOM classes to `FluentDOM\DOM\*`
- Changed: `FluentDOM\Nodes\Creator` to `FluentDOM\Creator`
- Changed: Moved internal classes into `FluentDOM\Utility`
- Removed: `Document::find()` and `Element::find()`
- Fixed #70: `FluentDOM\Query::get($position)` should return a node
- Fixed #73, Creator forgets namespaces 
- Added #73: `FluentDOM\XMLWriter::collapse()` collapse DOM nodes
- Added #74: `FluentDOM\XMLReader::attachStream()` attach stream to read from

6.2.0
-----

- Fixed #73: Creator forgot namespaces
- Added: `$filter` argument (callable) to `XMLReader::next()` and `XMLReader::read()`
- Added: `FluentDOM\XMLReader\Iterator`
- Added: `FluentDOM\XMLReader\SiblingIterator`

6.1.0
-----

- Added #58: FluentDOM\Nodes\Creator::$optimizeNamespaces (default TRUE) optimizes
  namespace definitions on the created document.
- Added: FluentDOM\NamespaceResolver interface
- Added: FluentDOM\Namespaces namespace resolver implementation
- Refactored: FluentDOM\Document::namespaces() now returns a FluentDOM\Namespaces instance
- Removed: FluentDOM\Document::getNamespace() - use FluentDOM\Document::namespaces() object
- Added #62: FluentDOM\XMLReader extend XMLReader with namespace registration 
- Added #63: FluentDOM\XMLWriter extend XMLWriter with namespace registration, 
    workaround for repeated namespace definitions 
- Added: FluentDOM\XMLReader::read() supports optional $namespaceUri parameter
- Added: FluentDOM\XMLReader::next() supports optional $name and $namespaceUri parameters
- Fixed #66: Improved Multibyte handling for HTML loader/serializer
- Added #60: FluentDOM\EntityReference

6.0.1
-----

- Fixed #57: FluentDOM\Text::replaceWholeText() should not remove entity definition

6.0.0
-----

- Changed: minimum PHP version is now 5.6
- Changed: you might now need to set an option to load files (security)
   - `[\FluentDOM\Options\ALLOW_FILE => TRUE]`
   - `[\FluentDOM\Options\IS_FILE => TRUE]`
- Changed: major cleanup/overhaul of the examples
- Refactored: Replace func_get_args() with variadics
- Added: HTML loader now supports 'html-fragment' and 'text/html-fragment'
- Added: XML/HTML loaders now support libxml options for the load methods
- Added: JsonDOM loader supports a callback for mapping keys to tag names
   The callback can be set using an option or JsonDOM::onMapKey().
- Changed: string arguments to methods like FluentDOM\Query::append() are now parsed as
    HTML fragments if the content type of the FluentDOM\Query instance is a HTML type.
- Changed: NULL values can now be set using FluentDOM\Query::attr(), FluentDOM\Query::css()
    and FluentDOM\Query::data() methods.
- Changed: FluentDOM\Nodes()/FluentDOM\Query() now keeps the content type used to load the
    document and use it for parsing fragments and serializing the document.
- Added: FluentDOM\Loadable::loadFragment() 
- Added: FluentDOM\Text::replaceWholeText() and FluentDOM\CdataSection::replaceWholeText()
- Added: FluentDOM::registerSerializerFactory(), register function/factory to create
    a serializer for a node.
- Added: FluentDOM\Loader\Options generic options for loaders (source type) 
- Added: FluentDOM\Exceptions\LoadingError exception interface 

5.3.0
-----

- Changed: CSS Selectors are now provided by small connector libraries
- Added: Interface FluentDOM\Xpath\Transformer, transform selectors to XPath
- Added: QuerySelectors are now supported if a CSS2XPath library is installed
- Refactored: extract some logic into private methods to reduce complexity 
- Implemented: cache validated qualified tag names, avoid repeating the validation with pcre.
    Can be configured using FluentDOM\QualifiedName::$cacheLimit
- Implemented: Avoid sorting if nodes should be in order (FluentDOM\Nodes), faster
- Implemented: Added FluentDOM\Nodes::FIND_FORCE_SORT, allow to force sorting for find()
- Fixed: _require.php explicit class loading

5.2.1
-----

- Implemented: allow associative arrays in JsonDOM loader
- Refactored: use === not just == 
- Fixed: HHVM now has native properties, adapted XPath to Document connection 

5.2.0
-----

- Added: FluentDOM\Loader\Text\CSV loads csv data into a xml document
- Added: FluentDOM::registerLoader() as a plugin system for loaders provided by other composer packages
- Added: FluentDOM\DocumentFragment is an extended version of the DOMDocumentFragment
  with support for the namespaces registered on the document object
- Added: FluentDOM node classes now implement interfaces from the DOM Living Standard
  ParentNode (except query*), NonDocumentTypeChildNode, ChildNode
- Added: FluentDOM::load() provides direct access to the loaders
- Added: FluentDOM\Loader\JSONx loads JSONx into a JsonDOM document
- Added: FluentDOM\Transformer\JSONx, transform a JsonDOM document to JSONx
- Changed: FluentDOM\Serializer\Json now writes json using the JsonDOM rules.
- Changed: FluentDOM\Nodes::find() now has options as the second argument, allow for match/filter modes
  match mode should improve performance and work better with xpath
- Refactored: FluentDOM\Nodes\Compare implements compare for nodes, allow to optimize sorting
- Refactored: most workarounds for HHVM are not necessary with HHVM 3.5, HHVM 3.5 is required for FluentDOM 5.2

5.1.1
-----

- Fixed: FluentDOM\Element::append() needs to ignore arrays and strings
  if checking for callable

5.1.0
-----

- Changed: All DOMNode descendants (FluentDOM\Document, FluentDOM\Element,
  FluentDOM\Text, ...) are now functors, allowing to evaluate an
  Xpath expression relative to them
- Changed: FluentDOM\Xpath is now a functor.
- Changed: FluentDOM\Element::append() is now longer restricted to
  FluentDOM\Appendable, but allows all kind of arguments
- Added: FluentDOM\ProcessingInstruction extends DOMProcessingInstruction
- Added: FluentDOM\Element::applyNamespaces(), adds xmlns
  attributes depending on the current namespace registration.
- Added: New FluentDOM\Nodes\Creator allows for compact node creation
- Added: FluentDOM\Loader\PHP\SimpleXml, load SimpleXmlElement
- Added: FluentDOM\Loader\PHP\PDO, load PDO statements
- Added: FluentDOM\Loader\Json\BadgerFish, load BadgerFish Json
- Added: FluentDOM\Loader\Json\JsonML, load JsonML
- Added: FluentDOM\Loader\Json\Rayfish, load Rayfish
- Added: FluentDOM\Loader\Lazy, Lazy load other loaders
- Added: FluentDOM\Serializer\Json\BadgerFish, generate BadgerFish Json
- Added: FluentDOM\Serializer\Json\JsonML, generate JsonML
- Added: FluentDOM\Serializer\Json\RabbitFish, generate RabbitFish Json
- Added: FluentDOM\Serializer\Json\Rayfish, generate Rayfish Json
- Added: FluentDOM\Transformer\Namespaces\Optimize, optimize
  namespace attributes, change prefixes
- Added: FluentDOM\Transformer\Namespaces\Replace, replace namespaces

5.0.2
-----

- Fixed: Remove null bytes in FluentDOM\Xpath::quote()
- Fixed: Namespace definition for Symfony CSS Selector

5.0.1
-----

- Fixed: Disable automatic namespace registration using the third
  argument to evaluate(), if activated using the property

5.0.0
-----

Complete Rewrite!
- PHP Namespaces, PSR-4 comatible, Composer support
- Original FluentDOM functionality is now merged in FluentDOM\Query.
- FluentDOM() function creates an FluentDOM\Query instance
- FluentDOM\Query has a new loader concept
- FluentDOM\Query allows to register a selector callback,
  allowing for CSS selectors using PhpCss
- Several classes that extend the default DOM classes, fixing bugs
  and provide convenience

- Changed: FluentDOM\Query::spawn() now clones the current object
- Changed: FluentDOM\Query::andSelf() renamed to addBack(), mark andSelf()
  as deprecated
- Added: FluentDOM\Query::html()
- Added: FluentDOM\Query::outerXml()
- Added: FluentDOM\Query::outerHtml()
- Added: FluentDOM\Document - extends DOMDocument
- Added: FluentDOM\Element - extends DOMElement
- Added: FluentDOM\Attribute - extends DOMAttr
- Added: FluentDOM\Comment - extends DOMComment
- Added: FluentDOM\CdataSection - extends DOMCdataSection
- Added: FluentDOM\Text - extends DOMText
- Added: FluentDOM\Xpath - extends DOMXpath
- Added: FluentDOM\Appendable - define objects appendable to a FluentDOM\Element
- Added: FluentDOM\XmlSerializable - define objects serializable to xml fragments

4.1.0
-----

For css() and attr() now property access is possible. HTML 5 data attributes are supported, too.

In PHP 5.3 an third argument was introduced to DOMXPath::evaluate(). This allows to disable the
automatic namespace registration. Because it is broken and completely wrong in the first place it
is disabled if possible. This improves performance, too.

- Changed: XPath expression do not register the namespaces of the context element if possible.
           This should improve performance and avoid conflicts.
- Changed: Tests now compatible to PHPUnit 3.5
- Changed: FluentDOMCore::load() now throws an exception if the source is empty
- Added: FluentDOM::$attr property access for xml attributes
- Added: FluentDOM::$css property access to the css option in the style attribute
- Added: FluentDOM::$data property access to HTML 5 data attributes
- Added: FluentDOM::data() read/write HTML 5 data attributes
- Added: FluentDOM::removeData() remove HTML 5 data attributes
- Added: FluentDOM::hasData() check if an element has HTML 5 data attributes
- Added: FluentDOM::reverse() reverse the order of the matched nodes.

4.0.0
-----

This version has been restructured because the class had grown to large. The new structure allows
better testing and inheritance.

It brings compatiblity to the jQuery 1.4 API changes. A callback argument is supported by many
methods.

- Implemented: FluentDOMLoader implementations now return a DOMNode or NULL,
               DOMDocument inherits from DOMNode
- Implemented: make the $contentType argument of FluentDOMLoader an reference,
               so the loader can change it, set the changed version
- Changed: Moved source files into subdirectory src
- Changed: Splitting changelog.txt from readme.txt
- Added: FluentDOMHighlighter example
- Added: FluentDOMCore::namespace(), register namespaces for xpath expressions
- Added: FluentDOMCore::unique(), sort an array of dom nodes and remove duplicates
- Implemented: FluentDOM::add() now supports a xml snippet, an expression with an optional context
               attribute or any kind of usable objects, nodes from other documents will be imported
- Removed: FluentDOM::node() functionality now covered by add()
- Added: FluentDOMCore::evaluate()
- Added: $context argument to FluentDOM::closest()
- Bugfix: FluentDOMCore::_getInnerXml() now returns the text content for a DOMText
- Added: FluentDOM::index()
- Added: FluentDOM::has()
- Added: FluentDOM::nextUntil(), FluentDOM::prevUntil(), FluentDOM::parentsUntil()
- Implemented: FluentDOM::children() now matches only elements (no text nodes any more)
- Added: FluentDOM::contents() matches all childnodes including textnodes
- Added: FluentDOM::last()
- Added: FluentDOM::first()
- Fixed: FluentDOMHandler::insertChildrenBefore(), new nodes had wrong order
- Renamed: FluentDOMCore::_getHandlers() to FluentDOMCore::_getHandler()
- Refactored: new FluentDOMCore::_applyContentToNodes() provides a generic way to manipulate
              selected nodes using a callback function, this is used by the most manipulation
              methods. FluentDOMHandler provides the callbacks
- Implemented: first argument of all callbacks is $node
              (unlike in js we can not set the scope in php, so this is not available)
- Implemented: FluentDOM::replaceWith() with callback
- Implemented: FluentDOM::after can now use a callback
- Implemented: FluentDOM::before can now use a callback
- Implemented: FluentDOMStyle::css() callback now has the following arguments: $node, $index, $value
- Added: FluentDOMLoaderStringJSON, loads a json string
- Implemented: FluentDOM::_wrap() with callback
- Tested: added @covers annotations
- Implemented: $context for FluentDOMCore::_test() ist now optional
- Added: FluentDOMCore::_isNodeList()
- Implemented: FluentDOMIterator now works for FluentDOMCore
- Implemented: FluentDOMCore::_spawn() is now publich and renamed to spawn()
- Implemented: FluentDOMCore::_push() is now publich and renamed to push()
- Implemented: splitting FluentDOMCore from FluentDOM
- Implemented: support additional parameter $attr in FluentDOM::node()
- Implemented: FluentDOM::append() supports function argument
- Implemented: support for callback parameters in FluentDOM::addClass(),
               FluentDOM::removeClass(),  FluentDOM::toggleClass()
- Implemented: support for empty parameter in  FluentDOM::removeClass()
- Added: Allow callback parameter for FluentDOM::text()
- Added: Allow callback parameter for FluentDOM::xml()
- Added: private function FluentDOM::_getContentFragment(),
         converts a string to a list of xml elements
- Added: optional parameter to FluentDOM::_isCallback(),
         allow or disallow global functions
- Implemented: changed attr() function parameters to match jQuery 1.4
- Added: FluentDOM::get(), retrieve elements as array
- Implemented: support negative position parameter for eq()

3.0.0
-----
- Documented: tutorial for find()
- Documented: optimized structure for html output
- Documented: custom loaders
- Implemented: moved require for FluentDOMLoader to top
- Documented: basic usage examples
- Documented: basic usage
- Documented: basic load
- Added: Tutorial - Create A Menu
- Added: source files for menu tutorial
- Added: missing test suite file
- Added: subdirectory FluentDOM, moved and renamed all matching files, changed include/require
- Added: check for empty NCName - Tested: empty NCName should throw an exception
- Documented: removing QName check @todo
- Implemented: QName relies now on exceptions
- Implemented: RFC compatible QName check with exact error responses
- Added: new usage examples
- Implemented: corrected faulty target file
- Documented: added missing phpdoc blocks, - Documented: fixed @return values
- Implemented: removed defined, but never used variables
- Implemented: closest() needs to match the current node, too
- Tested: closest() needs to match the current node, too
- Implemented: improved the closest() example, explaning a possible problem
- Implemented: jQuery 1.3 traversing method: closest
               added links to jQuery and schlitt.info - corrected misspelled words
- Documented: added description from webpage
- Documented: added tutorial file and linked it
- Documented: fixed descriptions, parameter types and names
- Documented: added @example tags for available examples
- Documented: added @example for interface methods
- Documented: fixed faulty svn:keyword identifier
- Documented: fixing documentation errors, adding example links
- Implemented: Column handling for FluentDOMLoaderPDO, much more compressed xml the loader now
               creates attributes for integer and decimal values and child elements for strings.
               The name of the column is used # as attribute and tag name.
- Implemented: FluentDOMLoaderPDO use object properties for the tag names
- Added: FluentDOMLoaderPDO
- create a FluentDOM from a PDOStatement
- Tested: FluentDOMLoaderPDO
- Fixed: SimpleXMLElementTest.php file name
- Tested: FluentDOM:removeAttr() with invalid parameter
- Implemented: _spawn() is now a private method
- Fixed: remove() now works with expression parameter
- Tested: remove() with expression parameter
- Added: removeAttr() with array parameter (list of attributes to remove)
- Added: removeAttr() with asterisk (*) parameter (removes all attributes)
- Tested: removeAttr() with array parameter
- Tested: removeAttr() with asterisk (*) parameter
- Fixed: DOMDocument are child classes from DOMNode
         but are invalid sources for the FluentDOMLoaderDOMNode, check DOMNode for an valid
         ownerDocument property
- Fixed: simple atom reader sample has to use the FluentDOM function an not the class directly
- changed FluentDOMTestCase should be an abstract class
- patch by Sebastian Bergmann

2.0.0
-----
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

1.0.0
-----

- initial release
