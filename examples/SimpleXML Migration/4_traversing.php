<?php
require(__DIR__.'/../../vendor/autoload.php');

$xml = <<<'XML'
<rss>
  <channel>
    <title>Example Feed</title>
    <item>
      <title>Example Item One</title>
    </item>
    <item>
      <title>Example Item Two</title>
    </item>
  </channel>
</rss>
XML;

/*
 * SimpleXML implements Traversable and __toString().
 * This allows to iterate nodes with foreach() and allow them
 * to be cast into strings.
 */
$element = simplexml_load_string($xml);
foreach ($element->channel->item as $item) {
  echo $item->title, "\n";
}

/*
 * FluentDOM depends heavily on XPath. If your
 * Xpath expression is a location path it will return a node list,
 * but using xpath functions, you can return strings, number or booleans.
 */
$document = FluentDOM::load($xml);
foreach ($document('/rss/channel/item') as $item) {
  echo $item('string(title)'), "\n";
}

/*
 * The nodes it self implement __toString(), so they can
 * be cast into a string.
 */
$document = FluentDOM::load($xml);
foreach ($document('/rss/channel/item/title') as $title) {
  echo $title, "\n";
}