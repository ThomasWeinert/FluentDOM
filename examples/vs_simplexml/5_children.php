<?php
require(dirname(__FILE__).'/../../vendor/autoload.php');

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

$element = simplexml_load_string($xml);
var_dump((string)$element->channel->children()[1]->title);

$document = FluentDOM::load($xml);
var_dump((string)$document('/rss/channel/item/title')->item(0));