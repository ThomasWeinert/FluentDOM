<?php
/**
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2019 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader {

  /**
   * Index for json format loaders
   */
  class Json extends Lazy {

    private static $_loaders = [
      Json\JsonDOM::class => Json\JsonDOM::CONTENT_TYPES,
      Json\JsonML::class => Json\JsonML::CONTENT_TYPES,
      Json\BadgerFish::class => Json\BadgerFish::CONTENT_TYPES,
      Json\Rayfish::class => Json\Rayfish::CONTENT_TYPES,
      Json\SimpleXML::class => Json\SimpleXML::CONTENT_TYPES,
      JSONx::class => JSONx::CONTENT_TYPES
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders);
    }
  }
}
