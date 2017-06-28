
<?php
require(__DIR__.'/../../../vendor/autoload.php');

$document = new FluentDOM\Document();
$document->loadHtml(
  '<!DOCTYPE html>
   <html><body><div id="navigation"/></body></html>'
);

$_ = FluentDOM::create();
$document
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

echo $document->saveHtml();