<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

require __DIR__.'/../../vendor/autoload.php';

$xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<atom:feed xmlns:atom="http://www.w3.org/2005/Atom" xmlns="http://www.w3.org/1999/xhtml">
  <atom:title>Example Feed</atom:title>
  <atom:link href="http://example.org/"/>
  <atom:entry>
    <atom:title>Atom-Powered Robots Run Amok</atom:title>
    <atom:id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</atom:id>
    <atom:updated>2003-12-13T18:30:02Z</atom:updated>
    <atom:summary type="text/xhtml">
      <p>Some text.</p>
    </atom:summary>
  </atom:entry>
</atom:feed>
XML;


/*
 * SimpleXMLs children() method is namespace aware. The first argument can
 * a namespace uri or prefix (I suggest you always provide the namespace uri).
 */
$element = simplexml_load_string($xml);
foreach ($element->children('http://www.w3.org/2005/Atom')->entry as $entry) {
  /** @var SimpleXMLElement $entry */
  echo $entry->children('http://www.w3.org/2005/Atom')->title, "\n";
}

/*
 * Or use SimpleXML to fetch the nodes, the problem here is that you will have
 * to register them again and again on each node.
 */
$element = simplexml_load_string($xml);
$element->registerXPathNamespace('a', 'http://www.w3.org/2005/Atom');
foreach ($element->xpath('a:entry') as $entry) {
  $entry->registerXPathNamespace('a', 'http://www.w3.org/2005/Atom');
  echo $entry->xpath('a:title')[0], "\n";
}

/*
 * In FluentDOM the namespaces can be registered on the document object.
 * All node objects will have access to the registered namespaces.
 */
$document = FluentDOM::load($xml);
$document->registerNamespace('a', 'http://www.w3.org/2005/Atom');
foreach ($document('/a:feed/a:entry') as $entry) {
  echo $entry('string(a:title)'), "\n";
}
