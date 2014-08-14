<?php
require_once(dirname(__FILE__).'/../../vendor/autoload.php');

$json = <<<JSON
{
  "firstName": "John",
  "lastName": "Smith",
  "age": 25,
  "address": {
    "streetAddress": "21 2nd Street",
    "city": "New York",
    "state": "NY",
    "postalCode": 10021
  },
  "phoneNumbers": [
    {
      "type": "home",
      "number": "212 555-1234"
    },
    {
      "type": "fax",
      "number": "646 555-4567"
    }
  ]
}
JSON;

$read = FluentDOM($json, 'text/json')->xpath;
$write = FluentDOM::create();
$write->formatOutput = TRUE;

echo $write(
  'numbers',
  $write->any(
    $read('//phoneNumbers/*/number'),
    function (FluentDOM\Element $node) use ($write) {
      return $write(
        'phone',
        ['type' => $node('string(parent::*/type)')],
        (string)$node
      );
    }
  )
);
