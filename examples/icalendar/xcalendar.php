<?php

require_once(__DIR__.'/../../vendor/autoload.php');

$dom = FluentDOM::load(__DIR__.'/example.ical', 'text/calendar');
$dom->formatOutput = TRUE;

echo $dom('string(//xCal:VEVENT/xCal:SUMMARY)');
echo "\n\n";
echo $dom->saveXml();