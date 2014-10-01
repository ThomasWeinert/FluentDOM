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
foreach ($element->channel->item as $item) {
  echo $item->title, "\n";
}

$document = FluentDOM::load($xml);
foreach ($document('/rss/channel/item') as $item) {
  echo $item('string(title)'), "\n";
}

$document = FluentDOM::load($xml);
foreach ($document('/rss/channel/item/title') as $title) {
  echo $title, "\n";
}