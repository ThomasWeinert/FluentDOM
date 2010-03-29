<?php
require('../../FluentDOM.php');

$fd = FluentDOM('find.xml');

// find the document element <root>
var_dump($fd->find('/root')->item(0)->nodeName);

//find the first <child> in <root>
var_dump($fd->find('/root/child')->item(0)->textContent);

//find the all <child>s anywhere in the document
foreach ($fd->find('//child') as $child) {
  var_dump($child->textContent);
}

//find the <root> first then the second element in it
var_dump($fd->find('/root')->find('*[2]')->item(0)->textContent);
