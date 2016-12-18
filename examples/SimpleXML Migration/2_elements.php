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
 * In SimpleXML you can use object property syntax to access the tag structure.
 * The property can be cast to string to fetch the direct text children content.
 */
$element = simplexml_load_string($xml);
echo $element->channel->title, "\n";

/*
 * FluentDOM uses Xpath to accomplish that. Nodes can be used like functions to
 * execute an Xpath expression in the context of the node. Xpath has a string cast
 * built in.
 */
$document = FluentDOM::load($xml);
echo $document('string(/rss/channel/title)'), "\n";


