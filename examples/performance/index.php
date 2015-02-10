<?php

require(__DIR__.'/../../vendor/autoload.php');

function benchmark(callable $callback, $callCount) {
  $start = microtime(true);
  for ($i = 0; $i < $callCount; $i++) {
    $callback();
  }
  return microtime(true) - $start;
}

$fd = FluentDOM('test.html', 'text/html');
echo benchmark(
  function() use ($fd) {
    $fd->find('//div[@class="test"]')->text();
  },
  100000
), "\n";

// FluentDOM >= 5.2 only
$fd = FluentDOM::load('test.html', 'text/html');
echo benchmark(
  function() use ($fd) {
    $fd('string(//div[@class="test"])');
  },
  100000
), "\n";


