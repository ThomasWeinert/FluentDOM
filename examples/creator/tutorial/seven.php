<?php
require('../../../vendor/autoload.php');

$_ = FluentDOM::create();
$_->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
$_->formatOutput = TRUE;

echo $_(
  'atom:feed',
  $_('atom:title', 'Example Feed')
);
