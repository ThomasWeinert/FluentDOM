<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Loader {

  /**
   * Index for json format loaders
   */
  class JsonLoaders extends LazyLoaders {

    private static array $_loaders = [
      Json\JsonDOMLoader::class => Json\JsonDOMLoader::CONTENT_TYPES,
      Json\JsonMLLoader::class => Json\JsonMLLoader::CONTENT_TYPES,
      Json\BadgerFishLoader::class => Json\BadgerFishLoader::CONTENT_TYPES,
      Json\RayfishLoader::class => Json\RayfishLoader::CONTENT_TYPES,
      Json\SimpleXMLLoader::class => Json\SimpleXMLLoader::CONTENT_TYPES,
      JSONxLoader::class => JSONxLoader::CONTENT_TYPES
    ];

    public function __construct() {
      parent::__construct();
      $this->addClasses(self::$_loaders);
    }
  }
}
