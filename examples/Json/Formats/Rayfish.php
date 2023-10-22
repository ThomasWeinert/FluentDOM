<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

require __DIR__.'/../../../vendor/autoload.php';

$xml = <<<'XML'
<person xmlns="urn:data" created="2006-11-11T19:23" modified="2006-12-31T23:59">
    <firstName>Robert</firstName>
    <lastName>Smith</lastName>
    <address type="home">
        <street>12345 Sixth Ave</street>
        <city>Anytown</city>
        <state>CA</state>
        <postalCode>98765-4321</postalCode>
    </address>
    <note>Hello <b>World</b>!</note>
</person>
XML;

$document = new DOMDocument();
$document->preserveWhiteSpace = FALSE;
$document->loadXML($xml);

echo "XML -> Rayfish\n\n";

$json = json_encode(
  new FluentDOM\Serializer\Json\RayfishSerializer($document), JSON_PRETTY_PRINT
);
echo $json;



