<?php
/**
*Collection of test for the FluentDOM class supporting PHP5.3
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2009 Bastian Feder, Thomas Weinert
*
* @package FluentDOM
* @subpackage unitTests
*/

/**
* load necessary files
*/
require_once dirname(__FILE__).'/FluentDOMTestCase.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for FluentDOM.
*
* @package FluentDOM
* @subpackage unitTests
*/
class FluentDOMTest_PHP5_3 extends FluentDOMTestCase {
  
  /**
  * @group TraversingFilter
  */
  public function testMap() {
    $fd = $this->getFixtureFromFile(__FUNCTION__);
    $fd->find('//p')
      ->append(
        implode(
          ', ',
          $fd
            ->find('//input')
            ->map(
              function ($node, $index) {
                $nodeFd = new FluentDOM();
                return $nodeFd->load($node)->attr("value");
              }
            )
        )
      );
    $this->assertFluentDOMEqualsXMLFile(__FUNCTION__, $fd);
  }
}
?>