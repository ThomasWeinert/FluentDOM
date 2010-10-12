<?php
/**
* Collection of tests for the FluentDOMCss class
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
require_once(dirname(__FILE__).'/../FluentDOMTestCase.php');
require_once(dirname(__FILE__).'/../../src/FluentDOM/Css.php');
require_once(dirname(__FILE__).'/../../src/FluentDOM/Style.php');

class FluentDOMCssTest extends FluentDOMTestCase {

  /**
  * @covers FluentDOMCss::__construct
  */
  public function testConstructorWithOwner() {
    $fd = $this->getMock('FluentDOMStyle');
    $css = new FluentDOMCss($fd);
    $this->assertAttributeSame($fd, '_fd', $css);
  }

  /**
  * @covers FluentDOMCss::offsetSet
  */
  public function testOffsetSetUpdatesAttributes() {
    $fd = new FluentDOMStyle();
    $fd->load('<sample style="width: 21px;"/>');
    $fd = $fd->find('/*');
    $css = new FluentDOMCss($fd);
    $css['width'] = '42px';
    $this->assertEquals(
      '<sample style="width: 42px;"/>', $fd->document->saveXml($fd->document->documentElement)
    );
  }

  /**
  * @covers FluentDOMCss::offsetSet
  */
  public function testOffsetSetRemovesAttributes() {
    $fd = new FluentDOMStyle();
    $fd->load('<sample style="width: 21px;"/>');
    $fd = $fd->find('/*');
    $css = new FluentDOMCss($fd);
    $css['width'] = '';
    $this->assertEquals(
      '<sample/>', $fd->document->saveXml($fd->document->documentElement)
    );
  }
}