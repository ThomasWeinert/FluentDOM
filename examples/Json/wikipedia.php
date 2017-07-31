<?php
require __DIR__.'/../../vendor/autoload.php';

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

$fd = FluentDOM($json, 'text/json');
$fd->find('/*/phoneNumbers/*[type="home"]/number')->text('789');

echo $fd;
