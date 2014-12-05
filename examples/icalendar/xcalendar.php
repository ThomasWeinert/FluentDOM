<?php

require_once(__DIR__.'/../../vendor/autoload.php');

$dom = FluentDOM::load(__DIR__.'/example.ical', 'text/calendar');
$dom->formatOutput = TRUE;
$dom->registerNamespace('xcal', 'urn:ietf:params:xml:ns:xcal');

echo $dom('string(//xcal:vevent/xcal:summary)');
echo "\n\n";
echo $dom->saveXml();