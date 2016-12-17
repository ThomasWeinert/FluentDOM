<?php
/**
 * The example compares the FluentDOM\Query Api with the extended DOM classes
 */

require(__DIR__.'/../../vendor/autoload.php');

function benchmark(callable $callback, $callCount) {
  $start = microtime(true);
  for ($i = 0; $i < $callCount; $i++) {
    $callback();
  }
  return microtime(true) - $start;
}

// FluentDOM\Query
$fd = FluentDOM('test.html', 'text/html', [FluentDOM\Loader\Options::IS_FILE => TRUE]);
echo benchmark(
  function() use ($fd) {
    $fd->find('//div[@class="test"]')->text();
  },
  100000
), "\n";

// extended DOM (FluentDOM >= 5.2), this is faster
$fd = FluentDOM::load('test.html', 'text/html', [FluentDOM\Loader\Options::IS_FILE => TRUE]);
echo benchmark(
  function() use ($fd) {
    $fd('string(//div[@class="test"])');
  },
  100000
), "\n";


