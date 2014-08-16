<?php
require('../../../vendor/autoload.php');

$dom = new FluentDOM\Document();
$dom->loadXml(
  '<div>
    <ul>
      <li><a href="http://fluentdom.org">FluentDOM</a></li>
      <li><a href="http://www.php.net">PHP</a></li>
    </ul>
  </div>'
);

$_ = FluentDOM::create();
$_->formatOutput = TRUE;

echo $_('p', $dom('//a'));
