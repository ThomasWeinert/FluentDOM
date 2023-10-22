<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

/**
 * The example compares the FluentDOM\Query Api with the extended DOM classes
 */

require __DIR__.'/../../vendor/autoload.php';

function benchmark(callable $callback, $callCount) {
  $start = microtime(true);
  for ($i = 0; $i < $callCount; $i++) {
    $callback();
  }
  return microtime(true) - $start;
}

// FluentDOM\Query
$fd = FluentDOM('test.html', 'text/html', [FluentDOM\Loader\LoaderOptions::IS_FILE => TRUE]);
echo benchmark(
  function() use ($fd) {
    $fd->find('//div[@class="test"]')->text();
  },
  100000
), "\n";

// extended DOM (FluentDOM >= 5.2), this is faster
$fd = FluentDOM::load('test.html', 'text/html', [FluentDOM\Loader\LoaderOptions::IS_FILE => TRUE]);
echo benchmark(
  function() use ($fd) {
    $fd('string(//div[@class="test"])');
  },
  100000
), "\n";


