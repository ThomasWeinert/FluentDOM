FluentDOM

FluentDOM provides an easy to use fluent interface for DOMDocument.

The idea was born in a workshop of Tobias Schlitt (schlitt.info) about the PHP XML 
extensions at the IPC Spring in Berlin. He used this idea to show XPath samples in the
session.

Many thanks to the jQuery (jquery.com) people for their work, who did an exceptional 
job describing their interfaces and providing examples. This saved us a lot of work.

We implemented most of the jQuery methods into FluentDOM, but here are
differences. Most important: we use XPath for expressions, not CSS selectors.
Since XPath is supported by the ext/xml extension, no extra parsing need to be done. 
This should be faster processing the selectors and btw it was easier for us to implement.
And as a nice topping it supports namespaces, too.

We implemented several php interfaces: Countable, Iterator, SeekableIterator and 
RecursiveItrerator. Even ArrayAccess is supported.

The jQuery method "next" has a conflict with the Iterator interface used in PHP. 
We attached the postfix 'Siblings' to each of these methods to get around the conflict:
E.g. 
  'next' => 'nextSiblings'
  'prev' => 'prevSiblings'
  
We think you got it.

Since FluentDOM works on XML documents, there is no method 'html()', but 'xml()'.

We support the string conversion using the magic __toString() method. It will
output the xml of the associated DOMDocument.

FluentDOM needs a document. The FluentDOM function (and the FluentDOM class
constructor) need a XML, a DOMNode or a DOMDocument to work with. The magic of jQuery's 
'${}' is provided by 'node()'.

XPath do not only match element nodes (nodes with a tag name and maybe children),
but text nodes, too. Which implecitly enhances FluentDOM to support them.

To be able to write phpUnit Tests and develop FluentDOM a lot of examples where written.
Most of them are copied and adapted from or are deeply inspired by the jQuery 
documentation. They are located in the 'examples' folder. 
Once again many thanks to the jQuery team.

Plans/ToDo:

1) FluentDOMStyle to support modifications of the style attribute
2) CSS to XPath expression translator