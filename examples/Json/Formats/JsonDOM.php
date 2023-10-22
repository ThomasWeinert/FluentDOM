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

header('Content-type: text/xml');
$fd = \FluentDOM(
  $json,
  'text/json',
  [
    // optional, map key to tag name
    \FluentDOM\Loader\Json\JsonDOMLoader::ON_MAP_KEY => function($key, $isArrayElement) {
      $map = [
        'phoneNumbers' => 'phoneNumber'
      ];
      if ($isArrayElement && isset($map[$key])) {
        return $map[$key];
      }
      return $key;
    }
  ]
);
echo $fd->formatOutput('text/xml');
