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

/**
 * In SimpleXML the ->children() method allows access to an array
 * containing the child elements
 */
$element = simplexml_load_string($xml);
var_dump((string)$element->channel->children()[1]->title);

/**
 * FluentDOM again uses Xpath. The expression results
 * in a DOMNodelist object. It has methods to access its
 * items and you can use iterator_to_array() to convert it into
 * an array.
 */
$document = FluentDOM::load($xml);
var_dump((string)$document('/rss/channel/item/title')->item(0));

/**
 * Since PHP 5.6.3, DOMNodelist allows to use array syntax.
 */
$document = FluentDOM::load($xml);
var_dump((string)$document('/rss/channel/item/title')[0]);
