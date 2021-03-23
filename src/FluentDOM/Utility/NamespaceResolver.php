<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\Utility {

  interface NamespaceResolver {

    /**
     * @param string $prefix
     * @return string|NULL
     */
    public function resolveNamespace(string $prefix): ?string;
  }
}
