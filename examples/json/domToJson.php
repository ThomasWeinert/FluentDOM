<?php
require_once(__DIR__.'/../../vendor/autoload.php');

$xml = <<<'XML'
<person created="2006-11-11T19:23" modified="2006-12-31T23:59">
    <firstName>Robert</firstName>
    <lastName>Smith</lastName>
    <address type="home">
        <street>12345 Sixth Ave</street>
        <city>Anytown</city>
        <state>CA</state>
        <postalCode>98765-4321</postalCode>
    </address>
</person>
XML;

$dom = new DOMDocument();
$dom->preserveWhiteSpace = FALSE;
$dom->loadXML($xml);

echo "JsonML\n\n";

echo new FluentDOM\Serializer\Json\JsonML($dom, JSON_PRETTY_PRINT);

echo "\n\nBadgerFish\n\n";

echo json_encode(new FluentDOM\Serializer\Json\BadgerFish($dom));



