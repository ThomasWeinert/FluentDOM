<?php

namespace FluentDOM\Utility {

  interface NamespaceResolver {

    /**
     * @param string $prefix
     * @return string|NULL
     */
    public function resolveNamespace(string $prefix);
  }
}