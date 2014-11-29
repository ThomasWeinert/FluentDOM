<?php
/**
 * Index for json format loaders
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  /**
   * Index for json format loaders
   */
  class Json extends Lazy {

    private $_loaders = [
      '\Json\JsonDOM' => ['json', 'application/json', 'text/json'],
      '\Json\JsonML' => ['jsonml', 'application/jsonml', 'application/jsonml+json'],
      '\Json\BadgerFish' => ['badgerfish', 'application/badgerfish', 'application/badgerfish+json'],
      '\JSONx' => ['jsonx', 'application/xml+jsonx']
    ];

    public function __construct() {
      $this->addClasses($this->_loaders, __NAMESPACE__);
    }
  }
}