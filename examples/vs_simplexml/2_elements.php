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
echo $element->channel->title, "\n";

$document = FluentDOM::load($xml);
echo $document('string(/rss/channel/title)'), "\n";


