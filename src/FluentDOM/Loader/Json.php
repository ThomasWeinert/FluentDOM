<?php
/**
 * Encapsulates the standard loaders (html, xml, json)
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  /**
   * Encapsulates the standard loaders (html, xml, json)
   */
  class Json extends Lazy {

    private $_loaders = [
      '\Json\JsonDOM' => ['json', 'application/json', 'text/json'],
      '\Json\JsonML' => ['jsonml', 'application/jsonml', 'application/jsonml+json'],
      '\Json\BadgerFish' => ['badgerfish', 'application/badgerfish', 'application/badgerfish+json']
    ];

    public function __construct() {
      $this->addClasses($this->_loaders, __NAMESPACE__);
    }
  }
}