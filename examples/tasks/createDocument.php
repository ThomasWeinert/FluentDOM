<?php
/**
*
* @version $Id $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
header('Content-type: text/plain');

require_once('../../FluentDOM.php');
echo FluentDOM($doc = new DOMDocument())
  ->append('<html/>')
  ->append($doc->createElement('body'))
  ->addClass('created')
  ->append('<h1>Hello World!</h1>')
  ->end()
  ->append('<p>Here I am.</p>');

?>