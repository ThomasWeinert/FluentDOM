
<?php
require(dirname(__FILE__).'/../../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->loadHtml(
  '<!DOCTYPE html>
   <html><body><div id="navigation"/></body></html>'
);

$_ = FluentDOM::create();
$dom
  ->getElementById('navigation')
  ->append(
    $_(
      'ul',
      ['class' => 'navigation'],
      $_(
        'li',
        $_('a', ['href' => 'http://fluentdom.org'], 'FluentDOM')
      )
    )
  );

echo $dom->saveHtml();