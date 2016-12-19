<?php
require_once(__DIR__.'/../../../vendor/autoload.php');

$xml = <<<'XML'
<person created="2006-11-11T19:23" modified="2006-12-31T23:59" xmlns="foo:bar" xmlns:bar="urn:oasis:names:tc:entity:xmlns:xml:catalog">
    <firstName>Robert</firstName>
    <lastName>Smith</lastName>
    <address type="home" xmlns="">
        <street>12345 Sixth Ave</street>
        <city>Anytown</city>
        <state>CA</state>
        <postalCode>98765-4321</postalCode>
    </address>
</person>
XML;

$document = FluentDOM::load($xml);

echo "XML -> JsonML\n\n";

$json = json_encode(
  new FluentDOM\Serializer\Json\JsonML($document), JSON_PRETTY_PRINT
);
echo $json;

echo "\n\nJsonML -> XML\n\n";

$document = FluentDOM::load($json, 'application/jsonml+json');
$document->formatOutput = TRUE;
echo $document->saveXMl();



