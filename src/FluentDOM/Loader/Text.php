<?php
/**
 * Index for text format loaders
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader {

  /**
   * Index for text format loaders
   */
  class Text extends Lazy {

    private $_loaders = [
      '\\Text\\ICalendar' => ['text/calendar'],
      '\\Text\\CSV' => ['text/csv'],
      '\\Text\\VCard' => ['text/vcard']
    ];

    public function __construct() {
      $this->addClasses($this->_loaders, __NAMESPACE__);
    }
  }
}