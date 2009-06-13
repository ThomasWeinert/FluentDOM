/**
*
* @version $Id $
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*/
<?php
header('Content-type: text/plain');

$xml = <<<XML
<html>
<head></head>
<body>
  <p class="blue">foo</p>
  <p class="blue highlight">bar</p>
</body>
</html>

XML;

require_once('../FluentDOM.php');
echo FluentDOM($xml)
  ->find('//p')
  ->toggleClass('highlight')
?>